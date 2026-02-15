<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'reviewable_type' => 'required|in:App\Models\Vendor,App\Models\Venue',
                'reviewable_id' => 'required|integer',
                'booking_id' => 'nullable|exists:bookings,id',
                'rating' => 'required|integer|min:1|max:5',
                'title' => 'nullable|string|max:255',
                'comment' => 'required|string|max:1000',
                'photos' => 'nullable|array|max:5',
            ]);

            // Check if user has already reviewed
            $existingReview = \App\Models\Review::where('user_id', auth()->id())
                ->where('reviewable_type', $validated['reviewable_type'])
                ->where('reviewable_id', $validated['reviewable_id'])
                ->exists();

            if ($existingReview) {
                return redirect()->back()
                    ->with('error', 'You have already reviewed this item');
            }

            DB::beginTransaction();

            $validated['user_id'] = auth()->id();
            $validated['status'] = 'pending';

            $review = \App\Models\Review::create($validated);

            DB::commit();

            Log::info("Review submitted", [
                'review_id' => $review->id,
                'user_id' => auth()->id()
            ]);

            return redirect()->back()
                ->with('success', 'Review submitted successfully. It will be visible after approval.');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to submit review: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to submit review. Please try again.')
                ->withInput();
        }
    }

    public function approve($id)
    {
        try {
            // Authorization check
            if (!auth()->user()->hasRole('admin')) {
                return redirect()->back()
                    ->with('error', 'Unauthorized');
            }

            $review = \App\Models\Review::findOrFail($id);

            DB::beginTransaction();

            $review->approve();

            DB::commit();

            return redirect()->back()
                ->with('success', 'Review approved successfully');
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Review not found');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to approve review: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to approve review. Please try again.');
        }
    }

    public function reject($id)
    {
        try {
            // Authorization check
            if (!auth()->user()->hasRole('admin')) {
                return redirect()->back()
                    ->with('error', 'Unauthorized');
            }

            $review = \App\Models\Review::findOrFail($id);

            DB::beginTransaction();

            $review->reject();

            DB::commit();

            return redirect()->back()
                ->with('success', 'Review rejected successfully');
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Review not found');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to reject review: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to reject review. Please try again.');
        }
    }
}