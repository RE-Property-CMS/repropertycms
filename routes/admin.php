<?php

use App\Http\Controllers\Backend\AdminsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Backend\AgentListingController;
use App\Http\Controllers\Backend\PropertiesController;
use App\Http\Controllers\Backend\SubscriberController;
use App\Http\Controllers\Backend\PagesController;
use App\Http\Controllers\Backend\SettingsController;
use App\Http\Controllers\Backend\DemoSeederController;
use App\Http\Controllers\Backend\DemoInviteController;
use App\Http\Controllers\Backend\LicenseController;

/*
|--------------------------------------------------------------------------
| Backend Admin Routes
|--------------------------------------------------------------------------
|
| All routes related to the Admin Panel.
| Protected by backend middleware.
|
*/

Route::namespace('Backend')->group(function () {
    Route::get('/', [AdminsController::class, 'admin']);

     /*
    |--------------------------------------------------------------------------
    | Guest Admin Routes
    |--------------------------------------------------------------------------
    */
    Route::namespace('Auth')->middleware('guest:admin')->group(function () {
        Route::get('sign-in', 'AuthenticatedSessionController@create')->name('login');
        Route::post('sign-in', 'AuthenticatedSessionController@store')->name('adminlogin');
    });

    /*
    |--------------------------------------------------------------------------
    | Authenticated Admin Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware(['admin', 'demo'])->group(function () {
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::namespace('Auth')->group(function () {
            Route::get('sign-out', [AuthenticatedSessionController::class, 'destroy'])->name('adminDestroy');
        });

    /*
    |--------------------------------------------------------------------------
    | Agent Management
    |--------------------------------------------------------------------------
    */
        Route::get('agent-listing', [AgentListingController::class, 'index'])->name('agentListing');
        Route::get('status', [AgentListingController::class, 'status'])->name('agentStatus');
        Route::get('delete', [AgentListingController::class, 'delete'])->name('agentDelete');
        Route::get('reset-password/{id}', [AgentListingController::class, 'resetPassword'])->name('agentResetPassword');
        Route::get('all-properties', [PropertiesController::class, 'index'])->name('properties');
        Route::get('expiry-due/{id}', [PropertiesController::class, 'ExpiryDue'])->name('ExpiryDue');
        Route::get('property-status', [PropertiesController::class, 'Property_Status'])->name('Property_Status');

    /*
    |--------------------------------------------------------------------------
    | Subscription Plans
    |--------------------------------------------------------------------------
    */

        Route::prefix('plans')->group(function () {
            Route::get('index', [\App\Http\Controllers\Backend\PlansController::class, 'index']);
        });

    /*
    |--------------------------------------------------------------------------
    | Subscribers
    |--------------------------------------------------------------------------
    */
        Route::get('subscriber', [SubscriberController::class, 'index'])->name('subscriber.index');
        Route::get('subscriptions', [DashboardController::class, 'subscriptions'])->name('subscriptions');
        Route::get('revenue', [DashboardController::class, 'revenue'])->name('revenue');

     /*
    |--------------------------------------------------------------------------
    | CMS Pages (Page Builder — super admin only)
    |--------------------------------------------------------------------------
    */
        Route::prefix('pages')->name('pages.')->middleware(['super_admin'])->group(function () {
            Route::get('/',                 [PagesController::class, 'index'])->name('lists');
            Route::get('/create',           [PagesController::class, 'create'])->name('create');
            Route::post('/',                [PagesController::class, 'store'])->name('store');
            Route::get('/{id}/edit',        [PagesController::class, 'edit'])->name('edit');
            Route::post('/{id}',            [PagesController::class, 'update'])->name('update');
            Route::get('/{id}/delete',      [PagesController::class, 'destroy'])->name('destroy');
        });

    /*
    |--------------------------------------------------------------------------
    | Demo Data Seeder (internal tool — all admins)
    |--------------------------------------------------------------------------
    */
        Route::prefix('demo')->name('demo.')->group(function () {
            Route::post('seed',  [DemoSeederController::class, 'seed'])->name('seed');
            Route::post('reset', [DemoSeederController::class, 'reset'])->name('reset');

            /*
            |--------------------------------------------------------------
            | Demo Sessions + Invitations (super admin only)
            |--------------------------------------------------------------
            */
            Route::middleware(['super_admin'])->group(function () {
                Route::get('sessions',               [DemoInviteController::class, 'sessions'])->name('sessions');
                Route::get('invite',                 [DemoInviteController::class, 'invite'])->name('invite');
                Route::post('invite',                [DemoInviteController::class, 'store'])->name('invite.store');
                Route::post('sessions/{id}/revoke',  [DemoInviteController::class, 'revoke'])->name('sessions.revoke');
                Route::post('sessions/{id}/resend',  [DemoInviteController::class, 'resend'])->name('sessions.resend');
            });
        });

    /*
    |--------------------------------------------------------------------------
    | License Management (owner installation only — LICENSE_OWNER=true)
    |--------------------------------------------------------------------------
    */
        if (env('LICENSE_OWNER') === 'true') {
            Route::prefix('licenses')->name('licenses.')->middleware(['super_admin'])->group(function () {
                Route::get('/',                  [LicenseController::class, 'dashboard'])->name('dashboard');
                Route::get('/buyers',            [LicenseController::class, 'buyers'])->name('buyers');
                Route::get('/buyers/create',     [LicenseController::class, 'createBuyer'])->name('buyers.create');
                Route::post('/buyers',           [LicenseController::class, 'storeBuyer'])->name('buyers.store');
                Route::get('/keys',              [LicenseController::class, 'keys'])->name('keys');
                Route::get('/keys/create',       [LicenseController::class, 'createKey'])->name('keys.create');
                Route::post('/keys',             [LicenseController::class, 'storeKey'])->name('keys.store');
                Route::post('/keys/{id}/revoke', [LicenseController::class, 'revokeKey'])->name('keys.revoke');
                Route::get('/verifications',     [LicenseController::class, 'verifications'])->name('verifications');
            });
        }

    /*
    |--------------------------------------------------------------------------
    | Integration Settings (Post-Setup Configuration)
    |--------------------------------------------------------------------------
    */
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/', [SettingsController::class, 'index'])->name('index');

            Route::get('mail', [SettingsController::class, 'mail'])->name('mail');
            Route::post('mail', [SettingsController::class, 'saveMail'])->name('mail.save');
            Route::post('mail/test', [SettingsController::class, 'testMail'])->name('mail.test');

            Route::get('stripe', [SettingsController::class, 'stripe'])->name('stripe');
            Route::post('stripe', [SettingsController::class, 'saveStripe'])->name('stripe.save');
            Route::post('stripe/test', [SettingsController::class, 'testStripe'])->name('stripe.test');

            Route::get('storage', [SettingsController::class, 'storage'])->name('storage');
            Route::post('storage', [SettingsController::class, 'saveStorage'])->name('storage.save');
            Route::post('storage/test', [SettingsController::class, 'testStorage'])->name('storage.test');

            Route::get('captcha', [SettingsController::class, 'captcha'])->name('captcha');
            Route::post('captcha', [SettingsController::class, 'saveCaptcha'])->name('captcha.save');
            Route::post('captcha/test', [SettingsController::class, 'testCaptcha'])->name('captcha.test');

            Route::get('maps', [SettingsController::class, 'maps'])->name('maps');
            Route::post('maps', [SettingsController::class, 'saveMaps'])->name('maps.save');

            Route::get('brand', [SettingsController::class, 'brand'])->name('brand');
            Route::post('brand', [SettingsController::class, 'saveBrand'])->name('brand.save');

            Route::get('docs', [SettingsController::class, 'docs'])->name('docs');
            Route::get('docs/download', [SettingsController::class, 'downloadDocs'])->name('docs.download');
            Route::get('docs/download-feature-guide', [SettingsController::class, 'downloadFeatureGuide'])->name('docs.download-feature-guide');
        });
    });

});
