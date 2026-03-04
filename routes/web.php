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
use App\Http\Controllers\GuestImportTemplateController;

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// ─── Public ───────────────────────────────────────────────────────────────────

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin'       => Route::has('login'),
        'canRegister'    => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion'     => PHP_VERSION,
    ]);
});

Route::post('/verify-otp', [AuthenticatedSessionController::class, 'verifyOTP'])->name('auth.verify-otp');
Route::post('/resend-otp', [AuthenticatedSessionController::class, 'resendOTP'])->name('resend-otp');

Route::get('events/{event}/invitation/{token}', [EventGuestController::class, 'showInvitation'])
    ->name('events.invitation.show');

// ─── Authenticated ────────────────────────────────────────────────────────────

Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/about', fn() => Inertia::render('About'))->name('about');

    // Profile
    Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Users
    Route::get('users',         [UserController::class, 'index'])->name('users.index');
    Route::get('users/create',  [UserController::class, 'create'])->name('users.create');
    Route::post('users/store',  [UserController::class, 'store'])->name('users.store');
    Route::get('users/{id}',    [UserController::class, 'edit'])->name('users.edit');
    Route::get('users/show',    [UserController::class, 'show'])->name('users.show');

    // Venues
    Route::resource('venues', VenueController::class);
    Route::post('venues/{id}/check-availability', [VenueController::class, 'checkAvailability'])
        ->name('venues.check-availability');

    // Events
    Route::resource('events', EventController::class);
    Route::post('events/{id}/cancel', [EventController::class, 'cancel'])->name('events.cancel');
    Route::post('events/{id}/bookings/create', [EventController::class, 'createBooking'])
        ->name('events.bookings.create');

    // ── Event Guests ──────────────────────────────────────────────────────────
    // IMPORTANT: all static segments must be defined BEFORE /{guestId} wildcards
    Route::prefix('events/{eventId}/guests')->name('events.guests.')->group(function () {

        // Static GET routes — listed first so /{guestId} wildcard never swallows them
        Route::get('/',                     [EventGuestController::class, 'index'])->name('index');
        Route::get('/create',               [EventGuestController::class, 'create'])->name('create');
        Route::get('/invitation-composer',  [EventGuestController::class, 'invitationComposer'])->name('invitation-composer');
        Route::get('/import-template',      [GuestImportTemplateController::class, '__invoke'])->name('import-template');

        // Static POST routes
        Route::post('/bulk-store',           [EventGuestController::class, 'bulkStore'])->name('bulk-store');
        Route::post('/bulk-destroy',         [EventGuestController::class, 'bulkDestroy'])->name('bulk-destroy');
        Route::post('/bulk-update',          [EventGuestController::class, 'bulkUpdate'])->name('bulk-update');
        Route::post('/send-invitations',     [EventGuestController::class, 'sendEmailInvitations'])->name('send-invitations');
        Route::post('/bulk-send-invitations',[EventGuestController::class, 'sendBulkInvitations'])->name('bulk-invites');

        // Wildcard routes — always last
        Route::get('/{guestId}/edit', [EventGuestController::class, 'edit'])->name('edit');
        Route::put('/{guestId}',      [EventGuestController::class, 'update'])->name('update');
        Route::delete('/{guestId}',   [EventGuestController::class, 'destroy'])->name('destroy');
    });

    Route::post('events/{event}/rsvp/{guest}', [EventGuestController::class, 'updateRsvp'])
        ->name('events.rsvp.update');

    // ── Event Tasks ───────────────────────────────────────────────────────────
    Route::prefix('events/{eventId}/tasks')->name('events.tasks.')->group(function () {
        Route::get('/',              [EventTaskController::class, 'index'])->name('index');
        Route::get('/create',        [EventTaskController::class, 'create'])->name('create');
        Route::post('/',             [EventTaskController::class, 'store'])->name('store');
        Route::put('/{taskId}',      [EventTaskController::class, 'update'])->name('update');
        Route::post('/{taskId}/complete', [EventTaskController::class, 'complete'])->name('complete');
        Route::delete('/{taskId}',   [EventTaskController::class, 'destroy'])->name('destroy');
    });

    // Bookings
    Route::resource('bookings', BookingController::class)->except(['edit', 'update']);
    Route::get('bookings/{id}/edit',      [BookingController::class, 'edit'])->name('bookings.edit');
    Route::put('bookings/{id}',           [BookingController::class, 'update'])->name('bookings.update');
    Route::post('bookings/{id}/confirm',  [BookingController::class, 'confirm'])->name('bookings.confirm');
    Route::post('bookings/{id}/cancel',   [BookingController::class, 'cancel'])->name('bookings.cancel');
    Route::post('bookings/{id}/complete', [BookingController::class, 'complete'])->name('bookings.complete');

    // Payments
    Route::get('bookings/{bookingId}/payments/create', [PaymentController::class, 'create'])->name('payments.create');
    Route::post('bookings/{bookingId}/payments',       [PaymentController::class, 'store'])->name('payments.store');
    Route::get('payments/{id}',                        [PaymentController::class, 'show'])->name('payments.show');

    // Invoices
    Route::get('invoices',                [InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('invoices/{id}',           [InvoiceController::class, 'show'])->name('invoices.show');
    Route::get('invoices/{id}/download',  [InvoiceController::class, 'download'])->name('invoices.download');
    Route::post('invoices/{id}/send',     [InvoiceController::class, 'sendEmail'])->name('invoices.send');

    // Vendors
    Route::get('vendors',           [VendorController::class, 'index'])->name('vendors.index');
    Route::get('vendors/create',    [VendorController::class, 'create'])->name('vendors.create');
    Route::get('vendors/edit/{id}', [VendorController::class, 'edit'])->name('vendors.edit');
    Route::get('vendors/{id}',      [VendorController::class, 'show'])->name('vendors.show');

    // Reviews
    Route::post('reviews',              [ReviewController::class, 'store'])->name('reviews.store');
    Route::post('reviews/{id}/approve', [ReviewController::class, 'approve'])->name('reviews.approve')->middleware('role:admin');
    Route::post('reviews/{id}/reject',  [ReviewController::class, 'reject'])->name('reviews.reject')->middleware('role:admin');

    // Reports
    Route::get('reports/messages',              [ReportsController::class, 'messages'])->name('reports.messages');
    Route::get('reports/messages/{id}',         [ReportsController::class, 'showMessage'])->name('messages.show');
    Route::post('reports/messages/{id}/retry',  [ReportsController::class, 'retryMessage'])->name('messages.retry');
    Route::post('reports/messages/bulk-resend', [ReportsController::class, 'bulkResend'])->name('messages.bulk-resend');
    Route::get('reports/export',                [ReportsController::class, 'export'])->name('export');
});

require __DIR__ . '/auth.php';