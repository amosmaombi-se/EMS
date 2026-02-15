<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use Illuminate\Validation\Rule;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class EventTaskController extends Controller
{
    public function index(Request $request, $eventId)
    {
        try {
            $event = Event::findOrFail($eventId);

            // Authorization check
            if ($event->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
                return redirect()->route('events.index')
                    ->with('error', 'Unauthorized');
            }

            $status = $request->input('status');
            $priority = $request->input('priority');
            $checklistId = $request->input('checklist_id');

            $tasksQuery = $event->tasks()->with(['assignedUser', 'checklist', 'completedByUser']);

            if ($status) {
                $tasksQuery->where('status', $status);
            }

            if ($priority) {
                $tasksQuery->where('priority', $priority);
            }

            if ($checklistId) {
                $tasksQuery->where('event_checklist_id', $checklistId);
            }

            $tasks = $tasksQuery->orderBy('sort_order')
                ->orderBy('due_date')
                ->paginate(50)
                ->withQueryString();

            $checklists = $event->checklists()->withCount('tasks')->get();

            $stats = [
                'total' => $event->tasks->count(),
                'completed' => $event->tasks->where('status', 'completed')->count(),
                'pending' => $event->tasks->where('status', 'pending')->count(),
                'in_progress' => $event->tasks->where('status', 'in_progress')->count(),
                'overdue' => $event->tasks()->where('due_date', '<', now())
                    ->whereNotIn('status', ['completed', 'cancelled'])->count(),
            ];

            return inertia('Events/Tasks/Index', [
                'event' => $event->load('eventType'),
                'tasks' => $tasks,
                'checklists' => $checklists,
                'stats' => $stats,
                'filters' => [
                    'status' => $status,
                    'priority' => $priority,
                    'checklist_id' => $checklistId,
                ]
            ]);
        } catch (ModelNotFoundException $e) {
            return redirect()->route('events.index')
                ->with('error', 'Event not found');
        } catch (Exception $e) {
            Log::error('Failed to fetch tasks: ' . $e->getMessage());
            return redirect()->route('events.show', $eventId)
                ->with('error', 'Failed to fetch tasks. Please try again.');
        }
    }

    public function store(Request $request, $eventId)
    {
        try {
            $event = Event::findOrFail($eventId);

            // Authorization check
            if ($event->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
                return redirect()->route('events.index')
                    ->with('error', 'Unauthorized');
            }

            $validated = $request->validate([
                'event_checklist_id' => 'nullable|exists:event_checklists,id',
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'priority' => ['required', Rule::in(['low', 'medium', 'high', 'urgent'])],
                'due_date' => 'nullable|date',
                'assigned_to' => 'nullable|exists:users,id',
            ]);

            DB::beginTransaction();

            $validated['event_id'] = $event->id;
            $validated['status'] = 'pending';

            $task = \App\Models\EventTask::create($validated);

            // Send notification to assigned user
            if ($task->assigned_to) {
                \App\Jobs\SendTaskAssignmentNotification::dispatch($task);
            }

            DB::commit();

            Log::info("Task created for event: {$event->title}", [
                'task_id' => $task->id,
                'event_id' => $event->id,
                'user_id' => auth()->id()
            ]);

            return redirect()->route('events.tasks.index', $event->id)
                ->with('success', 'Task created successfully');
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return redirect()->route('events.index')
                ->with('error', 'Event not found');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to create task: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to create task. Please try again.')
                ->withInput();
        }
    }

    public function update(Request $request, $eventId, $taskId)
    {
        try {
            $event = Event::findOrFail($eventId);

            // Authorization check
            if ($event->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
                return redirect()->route('events.index')
                    ->with('error', 'Unauthorized');
            }

            $task = \App\Models\EventTask::where('event_id', $eventId)
                ->findOrFail($taskId);

            $validated = $request->validate([
                'title' => 'sometimes|required|string|max:255',
                'description' => 'nullable|string',
                'priority' => ['sometimes', Rule::in(['low', 'medium', 'high', 'urgent'])],
                'status' => ['sometimes', Rule::in(['pending', 'in_progress', 'completed', 'cancelled'])],
                'due_date' => 'nullable|date',
                'assigned_to' => 'nullable|exists:users,id',
            ]);

            DB::beginTransaction();

            $oldAssignedTo = $task->assigned_to;

            $task->update($validated);

            // Send notification if assigned user changed
            if (isset($validated['assigned_to']) && $validated['assigned_to'] !== $oldAssignedTo) {
                \App\Jobs\SendTaskAssignmentNotification::dispatch($task);
            }

            DB::commit();

            return redirect()->route('events.tasks.index', $event->id)
                ->with('success', 'Task updated successfully');
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return redirect()->route('events.index')
                ->with('error', 'Task not found');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to update task: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to update task. Please try again.')
                ->withInput();
        }
    }

    public function complete(Request $request, $eventId, $taskId)
    {
        try {
            $event = Event::findOrFail($eventId);
            
            $task = \App\Models\EventTask::where('event_id', $eventId)
                ->findOrFail($taskId);

            // Authorization check - can be completed by assigned user or event owner
            if ($task->assigned_to !== auth()->id() && 
                $event->user_id !== auth()->id() && 
                !auth()->user()->hasRole('admin')) {
                return redirect()->route('events.tasks.index', $event->id)
                    ->with('error', 'Unauthorized');
            }

            DB::beginTransaction();

            $task->markAsCompleted(auth()->user());

            DB::commit();

            Log::info("Task completed: {$task->title}", [
                'task_id' => $task->id,
                'event_id' => $event->id,
                'user_id' => auth()->id()
            ]);

            return redirect()->route('events.tasks.index', $event->id)
                ->with('success', 'Task marked as completed');
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return redirect()->route('events.index')
                ->with('error', 'Task not found');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to complete task: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to complete task. Please try again.');
        }
    }

    public function destroy($eventId, $taskId)
    {
        try {
            $event = Event::findOrFail($eventId);

            // Authorization check
            if ($event->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
                return redirect()->route('events.index')
                    ->with('error', 'Unauthorized');
            }

            $task = \App\Models\EventTask::where('event_id', $eventId)
                ->findOrFail($taskId);

            DB::beginTransaction();

            $taskTitle = $task->title;
            $task->delete();

            DB::commit();

            Log::info("Task deleted: {$taskTitle}", [
                'event_id' => $event->id,
                'user_id' => auth()->id()
            ]);

            return redirect()->route('events.tasks.index', $event->id)
                ->with('success', 'Task deleted successfully');
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return redirect()->route('events.index')
                ->with('error', 'Task not found');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete task: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to delete task. Please try again.');
        }
    }
}