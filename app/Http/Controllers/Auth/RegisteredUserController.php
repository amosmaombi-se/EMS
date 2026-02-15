<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Microfinance;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Services\ActivityService;


class RegisteredUserController extends Controller
{

    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }


    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:' . User::class,
            'phone' => 'required|string|max:20',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'organization_name' => 'required|string|max:255',
            'registration_number' => 'required|string|max:100',
            'address' => 'required|string|max:500',
            'terms' => 'required|accepted',
        ]);

        DB::beginTransaction();

        try {
            $microfinance = Microfinance::create([
                'uuid' => Str::uuid(),
                'name' => strtoupper($request->organization_name),
                'registration_number' => $request->registration_number,
                'address' => strtoupper($request->address),
                'email' => $request->email,
                'phone' => $this->formatPhoneNumber($request->phone),
            ]);

            $user = User::create([
                'uuid' => Str::uuid(),
                'name' => strtoupper($request->name),
                'email' => $request->email,
                'phone' => $this->formatPhoneNumber($request->phone),
                'password' => Hash::make($request->password),
                'provider' => 'email',
                'microfinance_id' => $microfinance->id,
                'google2fa_secret' => 'google',
                'google2fa_enabled' => false,
                'provider_id' => 'google',
            ]);

            $fiscalYear = $this->createInitialFiscalYear($microfinance->id);

            $metadata = [
                'user_id' => $user->id,
                'user_uuid' => $user->uuid,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'microfinance_id' => $microfinance->id,
                'microfinance_uuid' => $microfinance->uuid,
                'microfinance_name' => $microfinance->name,
                'registration_number' => $microfinance->registration_number,
                'fiscal_year_id' => $fiscalYear->id,
                'fiscal_year_code' => $fiscalYear->year_code,
                'created_at' => now()->toIso8601String(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ];


            app(ActivityService::class)->logActivity(
                microfinanceId: $microfinance->id,
                activityTypeName: 'user_registration',
                description: "New user registration with microfinance: {$microfinance->name}",
                metadata: $metadata,
                outcome: "User successfully registered",
                location: $request->ip(),
                isSystemGenerated: false
            );

            DB::commit();

            event(new Registered($user));
            Auth::login($user);

            return redirect(RouteServiceProvider::HOME)->with('success', 'Registration successful. Welcome to MicroFinance!');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Registration failed: ' . $e->getMessage(), [
                'request_data' => $request->except(['password', 'password_confirmation']),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return redirect()->back()->withInput()->withErrors(['error' => 'Registration failed: ' . $e->getMessage()]);
        }
    }

    private function createInitialFiscalYear($microfinanceId)
    {
        Log::info("Creating initial fiscal year for microfinance ID: {$microfinanceId}");

        $savingCycle = SavingCycle::where('microfinance_id', $microfinanceId)->orderBy('start_date', 'asc')->first();

        if ($savingCycle) {
            $startDate = Carbon::parse($savingCycle->start_date);
            $hasSavingCycle = true;
            Log::info("Using SavingCycle start date: {$startDate->format('Y-m-d')}");
        } else {
            $currentYear = now()->year;
            $startDate = Carbon::createFromDate($currentYear, 1, 1)->startOfDay();

            if (now()->month > 6) {
                $startDate = Carbon::createFromDate($currentYear + 1, 1, 1)->startOfDay();
            }
            $hasSavingCycle = false;
            Log::info("Using default start date: {$startDate->format('Y-m-d')}");
        }

        $endDate = $startDate->copy()->addMonths(12)->subDay();
        $yearCode = 'FY' . $startDate->year;

        $fiscalYear = FiscalYear::create([
            'uuid' => Str::uuid(),
            'microfinance_id' => $microfinanceId,
            'year_code' => $yearCode,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => 'active',
        ]);

        Log::info("Created initial fiscal year: {$yearCode} for microfinance ID: {$microfinanceId}");

        $trigger = $hasSavingCycle ? 'registration_with_saving_cycle' : 'registration_without_saving_cycle';
        $this->logFiscalYearCreationActivity($fiscalYear, $trigger);

        return $fiscalYear;
    }

     private function getCurrentFiscalYear($microfinanceId)
    {
        if (!$microfinanceId) {
            throw new \Exception("Microfinance ID is required to get fiscal year.");
        }
        
        $fiscalYear = FiscalYear::where('microfinance_id', $microfinanceId)
            ->where('status', 'active')
            ->first();
            
        if (!$fiscalYear) {
            Log::info("No active fiscal year found for microfinance ID: {$microfinanceId}, attempting to create one");
            
            $hasSavingCycle = false;
            $savingCycle = SavingCycle::where('microfinance_id', $microfinanceId)
                ->orderBy('start_date', 'asc')
                ->first();
                
            if (!$savingCycle) {
                Log::warning("No SavingCycle found for microfinance ID: {$microfinanceId}, creating default fiscal year");
                $currentYear = now()->year;
                $startDate = Carbon::createFromDate($currentYear, 1, 1)->startOfDay();
                
                if (now()->month > 6) {
                    $startDate = Carbon::createFromDate($currentYear + 1, 1, 1)->startOfDay();
                }
            } else {
                $startDate = Carbon::parse($savingCycle->start_date);
                $hasSavingCycle = true;
            }
            
            $endDate = $startDate->copy()->addMonths(12)->subDay();
            $yearCode = 'FY' . $startDate->year;
            
            try {
                $fiscalYear = FiscalYear::create([
                    'uuid' => Str::uuid(),
                    'microfinance_id' => $microfinanceId,
                    'year_code' => $yearCode,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'status' => 'active',
                ]);
                
                Log::info("Created new fiscal year: {$yearCode} for microfinance ID: {$microfinanceId}");
                
                $trigger = $hasSavingCycle ? 'missing_fiscal_year' : 'missing_fiscal_year_and_saving_cycle';
                $this->logFiscalYearCreationActivity($fiscalYear, $trigger);
                
            } catch (\Exception $e) {
                Log::error("Failed to create fiscal year: " . $e->getMessage(), [
                    'microfinance_id' => $microfinanceId,
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]);
                throw new \Exception("Could not create fiscal year: " . $e->getMessage());
            }
        }
        
        return $fiscalYear;
    }

 
    private function logFiscalYearCreationActivity($fiscalYear, $trigger)
    {
        $metadata = [
            'fiscal_year_id' => $fiscalYear->id,
            'fiscal_year_uuid' => $fiscalYear->uuid,
            'year_code' => $fiscalYear->year_code,
            'start_date' => $fiscalYear->start_date->toIso8601String(),
            'end_date' => $fiscalYear->end_date->toIso8601String(),
            'trigger' => $trigger,
            'created_at' => now()->toIso8601String()
        ];

        app(ActivityService::class)->logActivity(
            microfinanceId: $fiscalYear->microfinance_id,
            activityTypeName: 'fiscal_year_created',
            description: "Fiscal year {$fiscalYear->year_code} created automatically",
            metadata: $metadata,
            outcome: "Fiscal year successfully created",
            location: request()->ip() ?? 'system',
            isSystemGenerated: true
        );
    }

    public function formatPhoneNumber($phoneNumber)
    {
        $phoneNumber = preg_replace('/[^0-9+]/', '', $phoneNumber);
        if (substr($phoneNumber, 0, 1) == '0') {
            return "255" . substr($phoneNumber, 1);
        } else if (substr($phoneNumber, 0, 4) == '+255') {
            return substr($phoneNumber, 1);
        } else if (substr($phoneNumber, 0, 3) == '255') {
            return $phoneNumber;
        } else if (strlen($phoneNumber) == 9 && is_numeric($phoneNumber)) {
            return "255" . $phoneNumber;
        } else {
            return $phoneNumber;
        }
    }

}
