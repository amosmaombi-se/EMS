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

class AuthenticatedSessionController extends Controller
{
    private const OTP_LENGTH = 6;
    private const OTP_EXPIRY_MINUTES = 5;
    private const MAX_RESEND_ATTEMPTS = 3;
    private const RESEND_COOLDOWN_SECONDS = 60;
    private const RATE_LIMIT_ATTEMPTS = 5;
    private const RATE_LIMIT_DECAY_MINUTES = 1;

    public function __construct(
        private readonly SmsService $smsService,
        private readonly EmailService $emailService,
        private readonly WhatsAppService $whatsappService
    ) {
    }

    public function create(): Response
    {
        return Inertia::render('Welcome', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    private function determineLoginType(string $username): string
    {
        return filter_var($username, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';
    }

    private function validateUserCredentials(array $credentials): User
    {
        $loginType = $this->determineLoginType($credentials['username']);

        if ($loginType === 'phone') {
            $credentials['username'] = $this->formatTanzanianNumber($credentials['username']);
        }

        $user = User::where($loginType, $credentials['username'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'username' => ['The provided credentials are incorrect.'],
            ]);
        }

        return $user;
    }

    private function generateOTP(): string
    {
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    private function generateAndCacheOTP(int $userId): string
    {
        $otp = $this->generateOTP();
        $key = "otp_{$userId}";
        Cache::put($key, $otp, now()->addMinutes(self::OTP_EXPIRY_MINUTES));
        return $otp;
    }

    private function sendOTPNotifications(User $user, string $otp, bool $isResend = false): array
    {
        $status = ['sms' => false, 'email' => false, 'whatsapp' => false];
        $loginType = $this->determineLoginType($user->email ?? $user->phone);

        $otpMessage = "Your OTP for login is: {$otp}. Valid for " . self::OTP_EXPIRY_MINUTES . " minutes.";

        // Send via WhatsApp (preferred method)
        if ($user->phone && ($loginType === 'phone' || $user->email)) {
            try {
                $whatsappData = [
                    'phone' => $user->phone,
                    'message' => "🔐 *Login Verification*\n\n{$otpMessage}\n\n_Do not share this code with anyone._",
                    'user_id' => $user->id,
                    'event_id' => null,
                    'priority' => 'high'
                ];

                $result = $this->whatsappService->sendMessage($whatsappData);
                $status['whatsapp'] = $result['success'] ?? false;

                if ($status['whatsapp']) {
                    Log::info('WhatsApp OTP sent successfully', [
                        'user_id' => $user->id,
                        'message_id' => $result['message_id'] ?? null
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('WhatsApp OTP sending failed', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        // Send via SMS (fallback or parallel delivery)
        if ($user->phone && ($loginType === 'phone' || $user->email)) {
            try {
                $smsData = [
                    'phone' => $user->phone,
                    'title' => 'Login OTP',
                    'message' => $otpMessage,
                    'subject' => 'Login Authentication',
                    'email' => $user->email,
                    'priority' => 'high',
                    'event_id' => null
                ];

                // $status['sms'] = $this->smsService->sendSms($smsData);

                if ($status['sms']) {
                    Log::info('SMS OTP sent successfully', [
                        'user_id' => $user->id
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('SMS OTP sending failed', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        // Send via Email
        if ($user->email && ($loginType === 'email' || $user->phone)) {
            try {
                $emailData = [
                    'phone' => $user->phone,
                    'title' => 'Login OTP',
                    'message' => $otpMessage,
                    'subject' => 'Login Authentication',
                    'email' => $user->email,
                    'priority' => 'high',
                    'event_id' => null
                ];

                $status['email'] = $this->emailService->sendNotification($emailData);

                if ($status['email']) {
                    Log::info('Email OTP sent successfully', [
                        'user_id' => $user->id
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Email OTP sending failed', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return $status;
    }

    public function store(Request $request): Response
    {
        try {
            if (RateLimiter::tooManyAttempts($this->throttleKey($request), self::RATE_LIMIT_ATTEMPTS)) {
                throw ValidationException::withMessages([
                    'username' => ['Too many login attempts. Please try again later.'],
                ]);
            }

            $credentials = $request->validate([
                'username' => 'required|string',
                'password' => 'required|string|min:8',
            ]);
            
            $loginType = $this->determineLoginType($credentials['username']);

            $validator = Validator::make(['username' => $credentials['username']], [
                'username' => $loginType === 'email'
                    ? 'email'
                    : 'regex:/^([0-9\s\-\+\(\)]*)$/|min:10'
            ]);

            if ($validator->fails()) {
                throw ValidationException::withMessages([
                    'username' => ['Invalid ' . $loginType . ' format.'],
                ]);
            }

            $user = $this->validateUserCredentials($credentials);
            $otp = $this->generateAndCacheOTP($user->id);
            $notificationStatus = $this->sendOTPNotifications($user, $otp);

            if (!$notificationStatus['sms'] && !$notificationStatus['email'] && !$notificationStatus['whatsapp']) {
                throw new \Exception('Failed to send OTP through any notification channel');
            }

            RateLimiter::clear($this->throttleKey($request));

            return Inertia::render('Auth/OTP', [
                'userId' => $user->id,
                'email' => $user->email,
                'phone' => $user->phone,
                'loginType' => $loginType,
                'notificationStatus' => $notificationStatus,
                'message' => $this->generateResponseMessage($notificationStatus, $loginType),
                'cooldownPeriod' => self::RESEND_COOLDOWN_SECONDS,
                'maxResendAttempts' => self::MAX_RESEND_ATTEMPTS
            ]);
        } catch (ValidationException $e) {
            Log::error('Login process failed', [
                'error' => $e->getMessage(),
                'username' => $request->username
            ]);
            RateLimiter::hit($this->throttleKey($request), self::RATE_LIMIT_DECAY_MINUTES * 60);
            return Inertia::render('Auth/Login', [
                'errors' => $e->errors(),
                'username' => $request->username
            ]);
        } catch (\Exception $e) {
            Log::error('Login process failed', [
                'error' => $e->getMessage(),
                'username' => $request->username
            ]);

            return Inertia::render('Auth/Login', [
                'errors' => ['username' => [$e->getMessage()]],
                'username' => $request->username
            ]);
        }
    }

    private function validateOTP(int $userId, string $otp): void
    {
        $cachedOTP = Cache::get("otp_{$userId}");

        if (!$cachedOTP) {
            throw ValidationException::withMessages([
                'otp' => ['OTP has expired. Please request a new one.'],
            ]);
        }

        if (!hash_equals($cachedOTP, $otp)) {
            throw ValidationException::withMessages([
                'otp' => ['Invalid OTP provided.'],
            ]);
        }
        Cache::forget("otp_{$userId}");
    }

    private function generateResponseMessage(array $notificationStatus, string $loginType): string
    {
        $sentChannels = [];
        
        if ($notificationStatus['whatsapp'] ?? false) {
            $sentChannels[] = 'WhatsApp';
        }
        if ($notificationStatus['sms'] ?? false) {
            $sentChannels[] = 'SMS';
        }
        if ($notificationStatus['email'] ?? false) {
            $sentChannels[] = 'email';
        }

        if (empty($sentChannels)) {
            return 'Failed to send OTP through any notification channel';
        }

        if (count($sentChannels) === 1) {
            return "OTP has been sent via {$sentChannels[0]}";
        }

        $lastChannel = array_pop($sentChannels);
        return 'OTP has been sent via ' . implode(', ', $sentChannels) . ' and ' . $lastChannel;
    }

    private function generateResendResponseMessage(array $notificationStatus, string $loginType, int $attempts): string
    {
        $baseMessage = $this->generateResponseMessage($notificationStatus, $loginType);
        $remainingAttempts = self::MAX_RESEND_ATTEMPTS - $attempts;
        return "{$baseMessage}. You have {$remainingAttempts} resend attempts remaining.";
    }

    private function validateResendAttempts(int $userId): void
    {
        $attempts = Cache::get('otp_resend_attempts_' . $userId, 0);
        $lastSentTime = Cache::get('last_otp_sent_' . $userId);

        if ($attempts >= self::MAX_RESEND_ATTEMPTS) {
            throw new \Exception('Maximum resend attempts reached. Please try logging in again.');
        }

        if ($lastSentTime && now()->diffInSeconds($lastSentTime) < self::RESEND_COOLDOWN_SECONDS) {
            $remainingSeconds = self::RESEND_COOLDOWN_SECONDS - now()->diffInSeconds($lastSentTime);
            throw new \Exception("Please wait {$remainingSeconds} seconds before requesting a new OTP.");
        }
    }

    public function verifyOTP(Request $request): RedirectResponse|Response
    {
        try {
            $data = $request->validate([
                'userId' => 'required|exists:users,id',
                'otp' => 'required|string|size:' . self::OTP_LENGTH,
            ]);

            $user = User::findOrFail($data['userId']);

            try {
                $this->validateOTP($data['userId'], $data['otp']);
            } catch (\Exception $e) {
                return Inertia::render('Auth/OTP', [
                    'errors' => ['otp' => ['Invalid OTP code. Please try again.']],
                    'userId' => $request->input('userId')
                ]);
            }

            Auth::login($user);
            $request->session()->regenerate();
            $this->clearOTPCache($user->id);
            
            return redirect()->intended(RouteServiceProvider::HOME);
        } catch (ValidationException $e) {
            return Inertia::render('Auth/OTP', [
                'errors' => $e->errors(),
                'userId' => $request->input('userId')
            ]);
        } catch (\Exception $e) {
            return Inertia::render('Auth/OTP', [
                'errors' => ['general' => [$e->getMessage() ?: 'An error occurred. Please try again.']],
                'userId' => $request->input('userId')
            ]);
        }
    }

    private function clearOTPCache(int $userId): void
    {
        Cache::forget("otp_{$userId}");
        Cache::forget("otp_resend_attempts_{$userId}");
        Cache::forget("last_otp_sent_{$userId}");
    }

    private function throttleKey(Request $request): string
    {
        return strtolower($request->input('email')) . '|' . $request->ip();
    }

    public function resendOTP(Request $request)
    {
        try {
            $data = $request->validate([
                'userId' => 'required|exists:users,id',
                'loginType' => 'required|in:email,phone'
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
            $attempts = Cache::increment('otp_resend_attempts_' . $user->id);
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
                'userId' => $user->id,
                'email' => $user->email,
                'phone' => $user->phone,
                'loginType' => $data['loginType'],
                'notificationStatus' => $notificationStatus,
                'message' => $this->generateResendResponseMessage(
                    $notificationStatus,
                    $data['loginType'],
                    $attempts
                ),
                'remainingAttempts' => self::MAX_RESEND_ATTEMPTS - $attempts,
                'cooldownPeriod' => self::RESEND_COOLDOWN_SECONDS
            ]);
        } catch (ValidationException $e) {
            Log::warning('OTP resend validation failed', [
                'user_id' => $request->userId,
                'errors' => $e->errors()
            ]);

            return Inertia::render('Auth/OTP', [
                'errors' => $e->errors(),
                'userId' => $request->userId,
                'loginType' => $request->loginType
            ]);
        } catch (\Exception $e) {
            Log::error('OTP resend failed', [
                'user_id' => $request->userId,
                'error' => $e->getMessage()
            ]);

            return Inertia::render('Auth/OTP', [
                'errors' => ['otp' => [$e->getMessage()]],
                'userId' => $request->userId,
                'loginType' => $request->loginType
            ]);
        }
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    public function logout(Request $request): \Illuminate\Http\JsonResponse
    {
        if ($request->user()) {
            $request->user()->currentAccessToken()->delete();
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }
}