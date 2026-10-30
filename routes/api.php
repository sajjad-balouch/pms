<?php

use App\Http\Controllers\Api\V1\Agent\AgentDashboardController;
use App\Http\Controllers\Api\V1\Agent\AgentPropertyController;
use App\Http\Controllers\Api\V1\Agent\DealController;
use App\Http\Controllers\Api\V1\Agent\DemandController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\Buyer\UnlockController;
use App\Http\Controllers\Api\V1\Buyer\WalletController;
use App\Http\Controllers\Api\V1\LeadController;
use App\Http\Controllers\Api\V1\Owner\BookingController;
use App\Http\Controllers\Api\V1\Owner\CustomerController;
use App\Http\Controllers\Api\V1\Owner\EmployeeController;
use App\Http\Controllers\Api\V1\Owner\ExpenseController;
use App\Http\Controllers\Api\V1\Owner\InstallmentController;
use App\Http\Controllers\Api\V1\Owner\OwnerDashboardController;
use App\Http\Controllers\Api\V1\Owner\RecoveryController;
use App\Http\Controllers\Api\V1\Owner\TownController;
use App\Http\Controllers\Api\V1\PublicController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Mobile App API — v1   (base URL: {APP_URL}/api/v1)
|--------------------------------------------------------------------------
| Auth: "Authorization: Bearer {token}" (token from /auth/login or /auth/register).
| Send "Accept: application/json" and optionally "Accept-Language: en|ur".
| Full reference: docs/mobile-api.md
*/

Route::prefix('v1')->middleware(['api.token', 'api.locale', 'throttle:api'])->group(function () {

    // ---------- Public (no login) ----------
    Route::middleware('throttle:api-auth')->group(function () {
        Route::post('auth/register', [AuthController::class, 'register']);
        Route::post('auth/login', [AuthController::class, 'login']);
    });

    Route::get('app/settings', [PublicController::class, 'settings']);
    Route::get('cities', [PublicController::class, 'cities']);
    Route::get('schemes', [PublicController::class, 'schemes']);
    Route::get('schemes/{town}', [PublicController::class, 'scheme']);
    Route::get('plots', [PublicController::class, 'plots']);
    Route::get('properties', [PublicController::class, 'properties']);
    Route::get('properties/{property}', [PublicController::class, 'property']);

    // ---------- Any logged-in user ----------
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('auth/me', [AuthController::class, 'me']);
        Route::put('auth/me', [AuthController::class, 'updateProfile']);
        Route::put('auth/password', [AuthController::class, 'changePassword']);
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::delete('auth/tokens', [AuthController::class, 'logoutEverywhere']);

        // Wallet & contact unlocks (buyers; agents/owners can use them too)
        Route::get('wallet', [WalletController::class, 'show']);
        Route::get('wallet/transactions', [WalletController::class, 'transactions']);
        Route::get('payment-methods', [WalletController::class, 'paymentMethods']);
        Route::post('wallet/top-ups', [WalletController::class, 'requestTopUp'])->middleware('throttle:api-topup');
        Route::post('properties/{property}/unlock', [UnlockController::class, 'unlockProperty'])->middleware('throttle:api-unlock');
        Route::post('plots/{plot}/unlock', [UnlockController::class, 'unlockPlot'])->middleware('throttle:api-unlock');
        Route::get('my/unlocks', [UnlockController::class, 'myUnlocks']);
        Route::get('my/saved-properties', [UnlockController::class, 'savedProperties']);
        Route::post('properties/{property}/save', [UnlockController::class, 'toggleSave']);

        // Leads CRM (agents and town owners)
        Route::middleware(['api.role:agent,town_owner', 'api.section:leads'])->group(function () {
            Route::get('leads', [LeadController::class, 'index']);
            Route::post('leads', [LeadController::class, 'store']);
            Route::get('leads/{lead}', [LeadController::class, 'show']);
            Route::put('leads/{lead}', [LeadController::class, 'update']);
            Route::patch('leads/{lead}/status', [LeadController::class, 'updateStatus']);
            Route::post('leads/{lead}/snooze', [LeadController::class, 'snooze']);
            Route::delete('leads/{lead}', [LeadController::class, 'destroy']);
        });

        // ---------- Agent ----------
        Route::prefix('agent')->middleware('api.role:agent')->group(function () {
            Route::get('dashboard', [AgentDashboardController::class, 'show']);

            Route::get('properties', [AgentPropertyController::class, 'index']);
            Route::post('properties', [AgentPropertyController::class, 'store']);
            Route::get('properties/{property}', [AgentPropertyController::class, 'show']);
            Route::post('properties/{property}', [AgentPropertyController::class, 'update']); // POST: multipart with images
            Route::delete('properties/{property}', [AgentPropertyController::class, 'destroy']);
            Route::delete('properties/{property}/images/{index}', [AgentPropertyController::class, 'deleteImage']);
            Route::post('properties/{property}/refresh', [AgentPropertyController::class, 'refresh']);

            Route::get('deals', [DealController::class, 'index']);
            Route::post('deals', [DealController::class, 'store']);
            Route::put('deals/{deal}', [DealController::class, 'update']);
            Route::delete('deals/{deal}', [DealController::class, 'destroy']);

            Route::get('demands', [DemandController::class, 'index']);
            Route::post('demands', [DemandController::class, 'store']);
            Route::put('demands/{demand}', [DemandController::class, 'update']);
            Route::post('demands/{demand}/close', [DemandController::class, 'close']);
            Route::get('demands/{demand}/contact', [DemandController::class, 'contact']);
        });

        // ---------- Town owner (and the owner's team, limited by permissions) ----------
        Route::prefix('owner')->middleware('api.role:town_owner')->group(function () {
            Route::get('me/permissions', [OwnerDashboardController::class, 'permissions']);
            Route::get('dashboard', [OwnerDashboardController::class, 'show'])->middleware('api.section:dashboard');

            Route::get('towns', [TownController::class, 'index']);
            Route::get('towns/{town}', [TownController::class, 'show']);
            Route::get('towns/{town}/plots', [TownController::class, 'plots'])->middleware('api.section:plots');

            Route::middleware('api.section:plots')->group(function () {
                Route::post('plots/{plot}/book', [BookingController::class, 'book']);
                Route::get('plots/{plot}/booking', [BookingController::class, 'show']);
                Route::post('plots/{plot}/cancel', [BookingController::class, 'cancel']);
                Route::post('plots/{plot}/transfer', [BookingController::class, 'transfer']);
            });

            Route::middleware('api.section:installments')->group(function () {
                Route::get('plots/{plot}/installments', [InstallmentController::class, 'index']);
                Route::post('installments/{installment}/receive', [InstallmentController::class, 'receive']);
                Route::post('installments/{installment}/undo', [InstallmentController::class, 'undo']);
            });

            Route::middleware('api.section:recovery')->group(function () {
                Route::get('recovery', [RecoveryController::class, 'index']);
                Route::get('plots/{plot}/followups', [RecoveryController::class, 'followups']);
                Route::post('plots/{plot}/followups', [RecoveryController::class, 'storeFollowup']);
            });

            Route::middleware('api.section:customers')->group(function () {
                Route::get('customers', [CustomerController::class, 'index']);
                Route::post('customers', [CustomerController::class, 'store']);
                Route::get('customers/{customer}', [CustomerController::class, 'show']);
                Route::put('customers/{customer}', [CustomerController::class, 'update']);
            });

            Route::middleware('api.section:expenses')->group(function () {
                Route::get('expenses', [ExpenseController::class, 'index']);
                Route::post('expenses', [ExpenseController::class, 'store']);
                Route::post('expenses/{expense}', [ExpenseController::class, 'update']); // POST: multipart with receipt
                Route::delete('expenses/{expense}', [ExpenseController::class, 'destroy']);
            });

            Route::middleware('api.section:employees')->group(function () {
                Route::get('employees', [EmployeeController::class, 'index']);
                Route::post('employees', [EmployeeController::class, 'store']);
                Route::put('employees/{employee}', [EmployeeController::class, 'update']);
                Route::get('employees/{employee}/payments', [EmployeeController::class, 'payments']);
                Route::post('employees/{employee}/payments', [EmployeeController::class, 'pay']);
            });
        });
    });

    // Unknown API URL => JSON 404 (not the website's HTML page)
    Route::fallback(fn () => response()->json(['success' => false, 'message' => 'Endpoint not found.'], 404));
});
