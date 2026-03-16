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
    Route::middleware('backend')->group(function () {
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
            Route::get('status', [\App\Http\Controllers\Backend\PlansController::class, 'status']);
            Route::get('add', [\App\Http\Controllers\Backend\PlansController::class, 'add']);
            Route::get('add/{id}', [\App\Http\Controllers\Backend\PlansController::class, 'add']);
            Route::get('delete/{id}', [\App\Http\Controllers\Backend\PlansController::class, 'delete']);
        });

    /*
    |--------------------------------------------------------------------------
    | Subscribers
    |--------------------------------------------------------------------------
    */
        Route::get('subscriber', [SubscriberController::class, 'index'])->name('subscriber.index');

     /*
    |--------------------------------------------------------------------------
    | CMS Pages
    |--------------------------------------------------------------------------
    */
        Route::prefix('pages')->name('pages.')->group(function () {
            Route::get('/', [PagesController::class, 'index'])->name('lists');
            Route::get('/create', [PagesController::class, 'create'])->name('create');
            Route::get('update/{id?}', 'PagesController@update')->name('update');
            Route::get('delete', 'PagesController@delete');
            Route::get('status', 'PagesController@status');
        });

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
        });
    });

});
