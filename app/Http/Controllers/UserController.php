<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\Role;
use Inertia\Inertia;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Builder;
use App\Services\SmsService;
use App\Services\EmailService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
class UserController extends Controller
{
    public function __construct(
        private readonly SmsService $smsService,
        private readonly EmailService $emailService
    ) {
    }

    protected function applySearch(Builder $query, $search)
    {
        try {
            return $query->where(function ($query) use ($search) {
                $query->whereRaw('LOWER(first_name) LIKE ?', ['%' . strtolower($search) . '%'])
                    ->orWhereRaw('LOWER(last_name) LIKE ?', ['%' . strtolower($search) . '%'])
                    ->orWhereRaw('LOWER(phone) LIKE ?', ['%' . strtolower($search) . '%'])
                    ->orWhereRaw('LOWER(email) LIKE ?', ['%' . strtolower($search) . '%']);
            });
        } catch (Exception $e) {
            Log::error('Error applying search: ' . $e->getMessage());
            throw $e;
        }
    }

    public function index(Request $request)
    {
        try {
            $search = $request->input('search');
            $role = $request->input('role');
            $perPage = 10;

            $usersQuery = User::query();

            if ($search) {
                $usersQuery = $this->applySearch($usersQuery, $search);
            }

            if ($role) {
                $usersQuery->where('role', $role);
            }

            $usersQuery = $usersQuery->orderBy('created_at', 'desc');
            $users = $usersQuery->paginate($perPage)->withQueryString();


            return Inertia::render('Users/Index', [
                'users' => UserResource::collection($users),
                'search' => $search,
                'role' => $role,
                'filters' => [
                    'search' => $search,
                    'role' => $role,
                ],
                'summary' => [
                    'totalUsers' => User::count(),
                    'totalAdmins' => $roleCounts['ADMIN'] ?? 0,
                    'totalStaff' => $roleCounts['STAFF'] ?? 0,
                    'totalRegularUsers' => $roleCounts['USER'] ?? 0,
                ],
            ]);
        } catch (Exception $e) {
            Log::error('Error fetching users: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load users. Please try again.');
        }
    }


    public function create()
    {
        $roles = Role::where('is_active', true)->get(['id', 'name']);

        return inertia('Users/Create', [
            'roles' => $roles
        ]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'role_ids' => 'required|array|min:1',
            'role_ids.*' => 'exists:roles,id',
            'password' => 'required|string|min:8|confirmed',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female',
            'is_active' => 'sometimes|boolean',
            'is_verified' => 'sometimes|boolean',
        ]);

        DB::beginTransaction();

        try {
            $password = $validatedData['password'] ?? Str::random(12);

            // Create user
            $user = User::create([
                'first_name' => $validatedData['first_name'],
                'last_name' => $validatedData['last_name'],
                'email' => $validatedData['email'],
                'phone' => $validatedData['phone'] ?? null,
                'password' => bcrypt($password),
                'date_of_birth' => $validatedData['date_of_birth'] ?? null,
                'gender' => $validatedData['gender'] ?? null,
                'is_active' => $validatedData['is_active'] ?? true,
                'is_verified' => $validatedData['is_verified'] ?? false,
                'email_verified_at' => $validatedData['is_verified'] ?? false ? now() : null,
            ]);

            // Attach roles
            if (isset($validatedData['role_ids'])) {
                $user->roles()->sync($validatedData['role_ids']);
            }

            $notificationData = [
                'user' => $user,
                'password' => $password, // Only include if you want to send the password via email (not recommended)
                'subject' => 'Your Account Has Been Created',
                'title' => 'Welcome to ' . config('app.name'),
                'message' => 'Your account has been created successfully. Please use the credentials below to login.',
                'action_url' => route('login'),
                'action_text' => 'Login to Account',
                'email' => $user->email,
            ];

            if ($user->email) {
                try {
                    $status = $this->emailService->sendNotification($notificationData);
                    Log::info('Password notification sent to user', [
                        'user_id' => $user->id,
                        'email' => $user->email,
                        'status' => $status ?? 'sent',
                    ]);

                } catch (Exception $e) {
                    Log::error('Failed to send password notification email', [
                        'user_id' => $user->id,
                        'email' => $user->email,
                        'error' => $e->getMessage(),
                    ]);
                    // Don't throw error, continue with user creation
                }
            }

            if ($user->phone && config('services.sms.enabled')) {
                try {
                    $this->smsService->sendSms(
                        $user->phone,
                        'Your account has been created. Please check your email for login credentials.'
                    );
                } catch (Exception $e) {
                    Log::warning('Failed to send SMS notification', [
                        'user_id' => $user->id,
                        'phone' => $user->phone,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            DB::commit();

            Log::info('User created successfully', [
                'user_id' => $user->id,
                'email' => $user->email,
                'roles' => $user->roles->pluck('name')->toArray(),
            ]);

            return redirect()->route('users.index')
                ->with('success', 'User created successfully. Password notification sent to user.');

        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Error creating user: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->except(['password', 'password_confirmation']),
            ]);

            return redirect()->back()
                ->with('error', 'Failed to create user. Please try again.')
                ->withInput($request->except(['password', 'password_confirmation']));
        }
    }
}
