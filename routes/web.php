<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\VenueController;
use App\Http\Controllers\EventGuestController;
use App\Http\Controllers\EventTaskController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ReportsController;

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;



Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


Route::post('/verify-otp', [AuthenticatedSessionController::class, 'verifyOTP'])->name('auth.verify-otp');
Route::post('resend-otp', [AuthenticatedSessionController::class, 'resendOTP'])->name('resend-otp');

Route::get('events/{event}/invitation/{token}', [EventGuestController::class, 'showInvitation'])
    ->name('events.invitation.show');


Route::middleware('auth')->group(function () {
    Route::get('/about', fn() => Inertia::render('About'))->name('about');

    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::get('/create/users', [UserController::class, 'create'])->name('users.create');
    Route::get('/create/show', [UserController::class, 'show'])->name('users.show');
    Route::post('users/store', [UserController::class, 'store'])->name('users.store');
    Route::get('users/{id}', [UserController::class, 'edit'])->name('users.edit');


    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Route::get('events', [EventController::class, 'index'])->name('events.index');
    Route::get('bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('invoices', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('venues', [VenueController::class, 'index'])->name('venues.index');
    Route::get('vendors', [VendorController::class, 'index'])->name('vendors.index');
});

Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Venues
    Route::resource('venues', VenueController::class);
    Route::post('venues/{id}/check-availability', [VenueController::class, 'checkAvailability'])
        ->name('venues.check-availability');

    // Events
    Route::resource('events', EventController::class);
    Route::post('events/{id}/cancel', [EventController::class, 'cancel'])->name('events.cancel');
    Route::post('events/{id}/bookings/create', [EventController::class, 'createBooking'])->name('events.bookings.create');


    // Event Guests
    Route::get('events/{eventId}/guests', [EventGuestController::class, 'index'])
        ->name('events.guests.index');
    Route::get('events/{eventId}/guests/create', [EventGuestController::class, 'create'])
        ->name('events.guests.create');

    Route::get('events/{eventId}/guests/invitation-composer', [EventGuestController::class, 'invitationComposer'])
        ->name('events.guests.invitation-composer');

    // Route::get('events/{eventId}/guests/{guestId}', [EventGuestController::class, 'show'])
    //     ->name('events.guests.show');
    Route::get('events/{eventId}/guests/{guestId}/edit', [EventGuestController::class, 'edit'])
        ->name('events.guests.edit');

    Route::post('events/{eventId}/guests/bulk-store', [EventGuestController::class, 'bulkStore'])
        ->name('events.guests.bulk-store');

    Route::put('events/{eventId}/guests/{guestId}', [EventGuestController::class, 'update'])
        ->name('events.guests.update');
    Route::delete('events/{eventId}/guests/{guestId}', [EventGuestController::class, 'destroy'])
        ->name('events.guests.destroy');
    Route::post('events/{eventId}/guests/bulk-destroy', [EventGuestController::class, 'bulkDestroy'])
        ->name('events.guests.bulk-destroy');
   
        
    Route::post('events/{eventId}/guests/send-invitations', [EventGuestController::class, 'sendEmailInvitations'])
        ->name('events.guests.send-invitations');
    Route::post('events/{eventId}/guests/bulk-send-invitations', [EventGuestController::class, 'sendBulkInvitations'])
        ->name('events.guests.bulk-invites');

    Route::post('events/{event}/rsvp/{guest}', [EventGuestController::class, 'updateRsvp'])
        ->name('events.rsvp.update');

    Route::post('events/{eventId}/guests/bulk-update', [EventGuestController::class, 'bulkUpdate'])
        ->name('events.guests.bulk-update');

    // Event Tasks
    Route::get('events/{eventId}/tasks', [EventTaskController::class, 'index'])
        ->name('events.tasks.index');
    Route::post('events/{eventId}/tasks', [EventTaskController::class, 'store'])
        ->name('events.tasks.store');
    Route::get('events/{eventId}/tasks/create', [EventTaskController::class, 'create'])
        ->name('events.tasks.create');
    Route::put('events/{eventId}/tasks/{taskId}', [EventTaskController::class, 'update'])
        ->name('events.tasks.update');
    Route::post('events/{eventId}/tasks/{taskId}/complete', [EventTaskController::class, 'complete'])
        ->name('events.tasks.complete');
    Route::delete('events/{eventId}/tasks/{taskId}', [EventTaskController::class, 'destroy'])
        ->name('events.tasks.destroy');

    // Bookings
    Route::resource('bookings', BookingController::class)->except(['edit', 'update']);
    Route::post('bookings/{id}/confirm', [BookingController::class, 'confirm'])
        ->name('bookings.confirm');
    Route::post('bookings/{id}/cancel', [BookingController::class, 'cancel'])
        ->name('bookings.cancel');
    Route::post('bookings/{id}/complete', [BookingController::class, 'complete'])
        ->name('bookings.complete');

    // Payments
    Route::get('bookings/{bookingId}/payments/create', [PaymentController::class, 'create'])
        ->name('payments.create');
    Route::post('bookings/{bookingId}/payments', [PaymentController::class, 'store'])
        ->name('payments.store');
    Route::get('payments/{id}', [PaymentController::class, 'show'])
        ->name('payments.show');

    // Invoices
    Route::get('invoices', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('invoices/{id}', [InvoiceController::class, 'show'])->name('invoices.show');
    Route::get('invoices/{id}/download', [InvoiceController::class, 'download'])
        ->name('invoices.download');
    Route::post('invoices/{id}/send', [InvoiceController::class, 'sendEmail'])
        ->name('invoices.send');

    // Vendors
    Route::get('vendors', [VendorController::class, 'index'])->name('vendors.index');
    Route::get('vendors/{id}', [VendorController::class, 'show'])->name('vendors.show');

    // Reviews
    Route::post('reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::post('reviews/{id}/approve', [ReviewController::class, 'approve'])
        ->name('reviews.approve')->middleware('role:admin');
    Route::post('reviews/{id}/reject', [ReviewController::class, 'reject'])
        ->name('reviews.reject')->middleware('role:admin');

    Route::get('reports/messages', [ReportsController::class, 'messages'])->name('reports.messages');
    Route::get('reports/messages/{id}', [ReportsController::class, 'showMessage'])->name('messages.show');
    Route::post('reports/messages/{id}/retry', [ReportsController::class, 'retryMessage'])->name('messages.retry');
    Route::post('reports/messages/bulk-resend', [ReportsController::class, 'bulkResend'])->name('messages.bulk-resend');
    
    Route::get('reports/export', [ReportsController::class, 'export'])->name('export');
});


require __DIR__ . '/auth.php';
