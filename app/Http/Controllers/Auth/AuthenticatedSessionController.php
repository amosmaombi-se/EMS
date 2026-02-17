<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Services\SmsService;
use App\Services\EmailService;
use App\Services\WhatsAppService;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class AuthenticatedSessionController extends Controller
{
    private const OTP_LENGTH               = 6;
    private const OTP_EXPIRY_MINUTES       = 5;
    private const MAX_RESEND_ATTEMPTS      = 3;
    private const RESEND_COOLDOWN_SECONDS  = 60;
    private const RATE_LIMIT_ATTEMPTS      = 5;
    private const RATE_LIMIT_DECAY_MINUTES = 1;

    public function __construct(
        private readonly SmsService      $smsService,
        private readonly EmailService    $emailService,
        private readonly WhatsAppService $whatsappService
    ) {}

    // =========================================================================
    // LOGIN PAGE
    // =========================================================================

    public function create(): Response
    {
        return Inertia::render('Welcome', [
            'canResetPassword' => Route::has('password.request'),
            'status'           => session('status'),
        ]);
    }

    // =========================================================================
    // HANDLE LOGIN SUBMISSION
    //
    //  ┌─────────────────────────────────────────────────────────────┐
    //  │  two_factor_enabled = 0  →  Send OTP → show Auth/OTP page  │
    //  │  two_factor_enabled = 1  →  Direct login → /dashboard      │
    //  └─────────────────────────────────────────────────────────────┘
    // =========================================================================

    public function store(Request $request): Response|RedirectResponse
    {
        try {
            // ── 1. Rate limiting ──────────────────────────────────────────────
            if (RateLimiter::tooManyAttempts($this->throttleKey($request), self::RATE_LIMIT_ATTEMPTS)) {
                throw ValidationException::withMessages([
                    'username' => ['Too many login attempts. Please try again later.'],
                ]);
            }

            // ── 2. Basic field validation ─────────────────────────────────────
            $credentials = $request->validate([
                'username' => 'required|string',
                'password' => 'required|string|min:8',
            ]);

            // ── 3. Determine login type (email or phone) ──────────────────────
            $loginType = $this->determineLoginType($credentials['username']);

            // ── 4. Format validation (email syntax / phone digits) ────────────
            $validator = Validator::make(['username' => $credentials['username']], [
                'username' => $loginType === 'email'
                    ? 'email'
                    : 'regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
            ]);

            if ($validator->fails()) {
                throw ValidationException::withMessages([
                    'username' => ['Invalid ' . $loginType . ' format.'],
                ]);
            }

            // ── 5. Verify credentials + is_active check ───────────────────────
            $user = $this->validateUserCredentials($credentials);

            // ── 6. Stamp last login metadata ──────────────────────────────────
            $user->update([
                'last_login_at' => Carbon::now(),
                'last_login_ip' => $request->ip(),
            ]);

            // ── 7. Clear rate limiter on successful auth ──────────────────────
            RateLimiter::clear($this->throttleKey($request));

            // ── 8. Branch on two_factor_enabled ──────────────────────────────
            if ((int) $user->two_factor_enabled === 1) {

                /*
                |------------------------------------------------------------
                | two_factor_enabled = 1
                | The user already has 2FA set up (two_factor_secret is set).
                | Trust the password alone → log in directly → /dashboard
                |------------------------------------------------------------
                */
                Auth::login($user);
                $request->session()->regenerate();

                Log::info('Direct login — two_factor_enabled = 1', [
                    'user_id' => $user->id,
                    'email'   => $user->email,
                    'ip'      => $request->ip(),
                ]);

                return redirect()->intended(RouteServiceProvider::HOME);
            }

            /*
            |----------------------------------------------------------------
            | two_factor_enabled = 0
            | 2FA is disabled, so we enforce a one-time OTP before granting
            | access — send the OTP then redirect to the OTP page.
            |----------------------------------------------------------------
            */
            $otp                = $this->generateAndCacheOTP($user->id);
            $notificationStatus = $this->sendOTPNotifications($user, $otp);

            // Ensure at least one channel delivered the OTP
            if (
                !$notificationStatus['sms']      &&
                !$notificationStatus['email']    &&
                !$notificationStatus['whatsapp']
            ) {
                throw new \Exception('Failed to send OTP through any notification channel');
            }

            Log::info('OTP dispatched — two_factor_enabled = 0', [
                'user_id'             => $user->id,
                'notification_status' => $notificationStatus,
                'ip'                  => $request->ip(),
            ]);

            return Inertia::render('Auth/OTP', [
                'userId'             => $user->id,
                'email'              => $user->email,
                'phone'              => $user->phone,
                'loginType'          => $loginType,
                'notificationStatus' => $notificationStatus,
                'message'            => $this->generateResponseMessage($notificationStatus, $loginType),
                'cooldownPeriod'     => self::RESEND_COOLDOWN_SECONDS,
                'maxResendAttempts'  => self::MAX_RESEND_ATTEMPTS,
            ]);

        } catch (ValidationException $e) {
            Log::warning('Login validation failed', [
                'username' => $request->username,
                'errors'   => $e->errors(),
            ]);

            RateLimiter::hit($this->throttleKey($request), self::RATE_LIMIT_DECAY_MINUTES * 60);

            return Inertia::render('Auth/Login', [
                'errors'   => $e->errors(),
                'username' => $request->username,
            ]);

        } catch (\Exception $e) {
            Log::error('Login process failed', [
                'error'    => $e->getMessage(),
                'username' => $request->username,
            ]);

            return Inertia::render('Auth/Login', [
                'errors'   => ['username' => [$e->getMessage()]],
                'username' => $request->username,
            ]);
        }
    }

    // =========================================================================
    // VERIFY OTP  (only reached when two_factor_enabled = 0)
    // =========================================================================

    public function verifyOTP(Request $request): RedirectResponse|Response
    {
        try {
            $data = $request->validate([
                'userId' => 'required|exists:users,id',
                'otp'    => 'required|string|size:' . self::OTP_LENGTH,
            ]);

            $user = User::findOrFail($data['userId']);

            // Validate OTP value
            try {
                $this->validateOTP($data['userId'], $data['otp']);
            } catch (\Exception $e) {
                return Inertia::render('Auth/OTP', [
                    'errors' => ['otp' => ['Invalid OTP code. Please try again.']],
                    'userId' => $request->input('userId'),
                ]);
            }

            // OTP passed → finalise login
            Auth::login($user);
            $request->session()->regenerate();
            $this->clearOTPCache($user->id);

            $user->update([
                'last_login_at' => Carbon::now(),
                'last_login_ip' => $request->ip(),
            ]);

            Log::info('OTP verified — login complete', [
                'user_id' => $user->id,
                'email'   => $user->email,
            ]);

            return redirect()->intended(RouteServiceProvider::HOME);

        } catch (ValidationException $e) {
            return Inertia::render('Auth/OTP', [
                'errors' => $e->errors(),
                'userId' => $request->input('userId'),
            ]);

        } catch (\Exception $e) {
            return Inertia::render('Auth/OTP', [
                'errors' => ['general' => [$e->getMessage() ?: 'An error occurred. Please try again.']],
                'userId' => $request->input('userId'),
            ]);
        }
    }

    // =========================================================================
    // RESEND OTP
    // =========================================================================

    public function resendOTP(Request $request): Response
    {
        try {
            $data = $request->validate([
                'userId'    => 'required|exists:users,id',
                'loginType' => 'required|in:email,phone',
            ]);

            $user = User::findOrFail($data['userId']);

            if ($data['loginType'] === 'email' && !$user->email) {
                throw new \Exception('No email address associated with this account.');
            }
            if ($data['loginType'] === 'phone' && !$user->phone) {
                throw new \Exception('No phone number associated with this account.');
            }

            $this->validateResendAttempts($user->id);

            $otp = $this->generateAndCacheOTP($user->id);
            Cache::put('last_otp_sent_' . $user->id, now(), now()->addMinutes(self::OTP_EXPIRY_MINUTES));
            $attempts           = Cache::increment('otp_resend_attempts_' . $user->id);
            $notificationStatus = $this->sendOTPNotifications($user, $otp, true);

            $hasSuccess = false;
            if ($data['loginType'] === 'email' && $notificationStatus['email']) {
                $hasSuccess = true;
            }
            if ($data['loginType'] === 'phone' && ($notificationStatus['sms'] || $notificationStatus['whatsapp'])) {
                $hasSuccess = true;
            }

            if (!$hasSuccess) {
                throw new \Exception('Failed to send OTP. Please try again.');
            }

            return Inertia::render('Auth/OTP', [
                'userId'             => $user->id,
                'email'              => $user->email,
                'phone'              => $user->phone,
                'loginType'          => $data['loginType'],
                'notificationStatus' => $notificationStatus,
                'message'            => $this->generateResendResponseMessage(
                    $notificationStatus,
                    $data['loginType'],
                    $attempts
                ),
                'remainingAttempts'  => self::MAX_RESEND_ATTEMPTS - $attempts,
                'cooldownPeriod'     => self::RESEND_COOLDOWN_SECONDS,
            ]);

        } catch (ValidationException $e) {
            Log::warning('OTP resend validation failed', [
                'user_id' => $request->userId,
                'errors'  => $e->errors(),
            ]);

            return Inertia::render('Auth/OTP', [
                'errors'    => $e->errors(),
                'userId'    => $request->userId,
                'loginType' => $request->loginType,
            ]);

        } catch (\Exception $e) {
            Log::error('OTP resend failed', [
                'user_id' => $request->userId,
                'error'   => $e->getMessage(),
            ]);

            return Inertia::render('Auth/OTP', [
                'errors'    => ['otp' => [$e->getMessage()]],
                'userId'    => $request->userId,
                'loginType' => $request->loginType,
            ]);
        }
    }

    // =========================================================================
    // LOGOUT (Web)
    // =========================================================================

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    // =========================================================================
    // LOGOUT (API / Sanctum)
    // =========================================================================

    public function logout(Request $request): \Illuminate\Http\JsonResponse
    {
        if ($request->user()) {
            $request->user()->currentAccessToken()->delete();
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Successfully logged out']);
    }

    // =========================================================================
    // PRIVATE HELPERS
    // =========================================================================

    /**
     * Returns 'email' or 'phone' based on the identifier format.
     */
    private function determineLoginType(string $username): string
    {
        return filter_var($username, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';
    }

    /**
     * Find the user, check password, check is_active.
     *
     * @throws ValidationException
     */
    private function validateUserCredentials(array $credentials): User
    {
        $loginType = $this->determineLoginType($credentials['username']);

        if ($loginType === 'phone') {
            $credentials['username'] = $this->formatTanzanianNumber($credentials['username']);
        }

        $user = User::where($loginType, $credentials['username'])->first();

        // Wrong credentials
        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'username' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Soft-deleted or deactivated account
        if (!(bool) $user->is_active) {
            throw ValidationException::withMessages([
                'username' => ['Your account has been deactivated. Please contact support.'],
            ]);
        }

        return $user;
    }

    /**
     * Generates a zero-padded 6-digit OTP string.
     */
    private function generateOTP(): string
    {
        return str_pad(random_int(0, 999999), self::OTP_LENGTH, '0', STR_PAD_LEFT);
    }

    /**
     * Generates an OTP and stores it in cache with a TTL.
     */
    private function generateAndCacheOTP(int $userId): string
    {
        $otp = $this->generateOTP();
        Cache::put("otp_{$userId}", $otp, now()->addMinutes(self::OTP_EXPIRY_MINUTES));

        Log::info('OTP generated', [
            'user_id'        => $userId,
            'expiry_minutes' => self::OTP_EXPIRY_MINUTES,
        ]);

        return $otp;
    }

    /**
     * Attempts delivery via WhatsApp → SMS → Email (all in parallel).
     * Returns an array of booleans keyed by channel name.
     */
    private function sendOTPNotifications(User $user, string $otp, bool $isResend = false): array
    {
        $status  = ['sms' => false, 'email' => false, 'whatsapp' => false];
        $label   = $isResend ? 'Resend OTP' : 'Login OTP';
        $message = "Your {$label} is: {$otp}. Valid for " . self::OTP_EXPIRY_MINUTES . " minutes. Do not share this code.";

        // WhatsApp ─────────────────────────────────────────────────────────────
        if ($user->phone) {
            try {
                $result             = $this->whatsappService->sendMessage([
                    'phone'    => $user->phone,
                    'message'  => "🔐 *Login Verification*\n\n{$message}\n\n_Do not share this code with anyone._",
                    'user_id'  => $user->id,
                    'event_id' => null,
                    'priority' => 'high',
                ]);
                $status['whatsapp'] = $result['success'] ?? false;

                Log::info('WhatsApp OTP ' . ($status['whatsapp'] ? 'sent' : 'failed'), ['user_id' => $user->id]);
            } catch (\Exception $e) {
                Log::error('WhatsApp OTP error', ['user_id' => $user->id, 'error' => $e->getMessage()]);
            }
        }

        // SMS ──────────────────────────────────────────────────────────────────
        if ($user->phone) {
            try {
                $result        = $this->smsService->sendSms([
                    'phone'        => $user->phone,
                    'message'      => $message,
                    'priority'     => 'high',
                    'type'         => 'otp',
                    'reference_id' => 'otp_' . $user->id,
                ]);
                $status['sms'] = $result['success'] ?? false;

                Log::info('SMS OTP ' . ($status['sms'] ? 'sent' : 'failed'), ['user_id' => $user->id]);
            } catch (\Exception $e) {
                Log::error('SMS OTP error', ['user_id' => $user->id, 'error' => $e->getMessage()]);
            }
        }

        // Email ────────────────────────────────────────────────────────────────
        if ($user->email) {
            try {
                $result          = $this->emailService->sendNotification([
                    'email'    => $user->email,
                    'phone'    => $user->phone,
                    'title'    => 'Login OTP',
                    'message'  => $message,
                    'subject'  => 'Your Login OTP Code',
                    'priority' => 'high',
                    'event_id' => null,
                ]);
                $status['email'] = $result['success'] ?? $result ?? false;

                Log::info('Email OTP ' . ($status['email'] ? 'sent' : 'failed'), ['user_id' => $user->id]);
            } catch (\Exception $e) {
                Log::error('Email OTP error', ['user_id' => $user->id, 'error' => $e->getMessage()]);
            }
        }

        return $status;
    }

    /**
     * Checks the cached OTP matches.  Forgets it on success.
     *
     * @throws ValidationException
     */
    private function validateOTP(int $userId, string $otp): void
    {
        $cachedOTP = Cache::get("otp_{$userId}");

        if (!$cachedOTP) {
            throw ValidationException::withMessages([
                'otp' => ['OTP has expired. Please request a new one.'],
            ]);
        }

        if (!hash_equals((string) $cachedOTP, (string) $otp)) {
            throw ValidationException::withMessages([
                'otp' => ['Invalid OTP provided.'],
            ]);
        }

        Cache::forget("otp_{$userId}");
    }

    /**
     * Guards against too-frequent or too-many resend requests.
     *
     * @throws \Exception
     */
    private function validateResendAttempts(int $userId): void
    {
        $attempts     = Cache::get('otp_resend_attempts_' . $userId, 0);
        $lastSentTime = Cache::get('last_otp_sent_' . $userId);

        if ($attempts >= self::MAX_RESEND_ATTEMPTS) {
            throw new \Exception('Maximum resend attempts reached. Please try logging in again.');
        }

        if ($lastSentTime && now()->diffInSeconds($lastSentTime) < self::RESEND_COOLDOWN_SECONDS) {
            $remainingSeconds = self::RESEND_COOLDOWN_SECONDS - now()->diffInSeconds($lastSentTime);
            throw new \Exception("Please wait {$remainingSeconds} seconds before requesting a new OTP.");
        }
    }

    /**
     * Wipes all OTP cache keys for the given user.
     */
    private function clearOTPCache(int $userId): void
    {
        Cache::forget("otp_{$userId}");
        Cache::forget("otp_resend_attempts_{$userId}");
        Cache::forget("last_otp_sent_{$userId}");
    }

    /**
     * Unique rate-limiter key: lowercase username + IP address.
     */
    private function throttleKey(Request $request): string
    {
        return strtolower((string) $request->input('username')) . '|' . $request->ip();
    }

  
    /**
     * Human-readable summary of which channels received the OTP.
     */
    private function generateResponseMessage(array $notificationStatus, string $loginType): string
    {
        $sentChannels = [];

        if ($notificationStatus['whatsapp'] ?? false) $sentChannels[] = 'WhatsApp';
        if ($notificationStatus['sms']      ?? false) $sentChannels[] = 'SMS';
        if ($notificationStatus['email']    ?? false) $sentChannels[] = 'email';

        if (empty($sentChannels)) {
            return 'Failed to send OTP through any notification channel';
        }

        if (count($sentChannels) === 1) {
            return "OTP has been sent via {$sentChannels[0]}";
        }

        $last = array_pop($sentChannels);
        return 'OTP has been sent via ' . implode(', ', $sentChannels) . ' and ' . $last;
    }

    /**
     * Same as above plus a "remaining resend attempts" suffix.
     */
    private function generateResendResponseMessage(array $notificationStatus, string $loginType, int $attempts): string
    {
        $base              = $this->generateResponseMessage($notificationStatus, $loginType);
        $remainingAttempts = self::MAX_RESEND_ATTEMPTS - $attempts;

        return "{$base}. You have {$remainingAttempts} resend attempts remaining.";
    }
}