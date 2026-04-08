<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\StripeWebhookController;

use App\Http\Controllers\Agent\AgentsController;
use App\Http\Controllers\Agent\AddressController;
use App\Http\Controllers\Agent\PaymentsController;
use App\Http\Controllers\Agent\PropertyController;
use App\Http\Controllers\PropertyController as PublicPropertyController;
use App\Http\Controllers\Agent\PropertyDocumentsController;
use App\Http\Controllers\Agent\PropertyFloorplansController;
use App\Http\Controllers\Agent\PropertyGalleriesController;
use App\Http\Controllers\Agent\PropertyImagesController;
use App\Http\Controllers\Agent\TopbarsController;
use App\Http\Controllers\Agent\VideoController;
use App\Http\Controllers\Agent\DashboardController;
use App\Http\Controllers\SetupController;

// TEMP: Mail preview routes — remove before production
// Route::get('/mail-preview/agent-registered', function () {
//     $agent = \App\Models\Agents::first() ?? (object)['first_name' => 'John'];
//     return view('mail.agent_registered', ['agent' => $agent]);
// });

// Error page previews — remove or restrict before production
Route::get('/preview-error/{code}', function (string $code) {
    $allowed = ['401', '403', '404', '419', '429', '500', '503'];
    if (! in_array($code, $allowed)) {
        abort(404);
    }
    return response()->view('errors.' . $code, [], (int) $code);
})->name('error.preview');

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
//

Route::prefix('setup')->name('setup.')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Landing / Key Verification (Step 0)
    |--------------------------------------------------------------------------
    */
    Route::get('/', [SetupController::class, 'index'])->name('index');
    Route::post('/verify-key', [SetupController::class, 'verifyKey'])->name('verify-key');

    /*
    |--------------------------------------------------------------------------
    | AJAX Credential Test Endpoints (no session-verified check needed)
    |--------------------------------------------------------------------------
    */
    Route::post('test-database', [SetupController::class, 'testDatabase'])->name('test-database');
    Route::post('test-mail', [SetupController::class, 'testMail'])->name('test-mail');
    Route::post('test-stripe', [SetupController::class, 'testStripe'])->name('test-stripe');
    Route::post('test-storage', [SetupController::class, 'testStorage'])->name('test-storage');

    /*
    |--------------------------------------------------------------------------
    | Database Repair (accessible when DB integrity check fails)
    |--------------------------------------------------------------------------
    */
    Route::get('database-repair', [SetupController::class, 'databaseRepair'])->name('database-repair');

    /*
    |--------------------------------------------------------------------------
    | Requirements
    |--------------------------------------------------------------------------
    */
    Route::get('requirements', [SetupController::class, 'requirements'])
        ->name('requirements');

    /*
    |--------------------------------------------------------------------------
    | Database
    |--------------------------------------------------------------------------
    */
    Route::get('database', [SetupController::class, 'database'])
        ->name('database');

    Route::post('database', [SetupController::class, 'saveDatabase'])
        ->name('database.save');
    /*
    |--------------------------------------------------------------------------
    | Admin
    |--------------------------------------------------------------------------
    */
    Route::get('admin', [SetupController::class, 'admin'])->name('admin');

    Route::post('admin', [SetupController::class, 'saveAdmin'])
        ->name('admin.save');
    /*
    |--------------------------------------------------------------------------
    | Mail
    |--------------------------------------------------------------------------
    */
    Route::get('mail', [SetupController::class, 'mail'])
        ->name('mail');

    Route::post('mail', [SetupController::class, 'saveMail'])
        ->name('mail.save');

    Route::post('mail-skip', [SetupController::class, 'skipMail'])
        ->name('mail.skip');

    /*
    |--------------------------------------------------------------------------
    | Stripe
    |--------------------------------------------------------------------------
    */
    Route::get('stripe', [SetupController::class, 'stripe'])
        ->name('stripe');

    Route::post('stripe', [SetupController::class, 'saveStripe'])
        ->name('stripe.save');

    Route::post('stripe-skip', [SetupController::class, 'skipStripe'])
        ->name('stripe.skip');
    /*
    |--------------------------------------------------------------------------
    | Storage
    |--------------------------------------------------------------------------
    */
    Route::get('storage', [SetupController::class, 'storage'])
        ->name('storage');

    Route::post('storage', [SetupController::class, 'saveStorage'])
        ->name('storage.save');

    Route::post('storage-skip', [SetupController::class, 'skipStorage'])
        ->name('storage.skip');
    /*
    |--------------------------------------------------------------------------
    | Captcha
    |--------------------------------------------------------------------------
    */
    Route::get('captcha', [SetupController::class, 'captcha'])
        ->name('captcha');

    Route::post('captcha', [SetupController::class, 'saveCaptcha'])
        ->name('captcha.save');

    Route::post('captcha-skip', [SetupController::class, 'skipCaptcha'])
        ->name('captcha.skip');
    /*
    |--------------------------------------------------------------------------
    | Brand Identity (Step 8 — skippable)
    |--------------------------------------------------------------------------
    */
    Route::get('branding', [SetupController::class, 'branding'])
        ->name('branding');

    Route::post('branding', [SetupController::class, 'saveBranding'])
        ->name('branding.save');

    Route::post('branding-skip', [SetupController::class, 'skipBranding'])
        ->name('branding.skip');

    /*
    |--------------------------------------------------------------------------
    | Final
    |--------------------------------------------------------------------------
    */
    Route::get('final', [SetupController::class, 'final'])
        ->name('final');

    Route::post('finish', [SetupController::class, 'finish'])
        ->name('finish');
});



// Go to Agent Login screen
Route::get('/', function () { return redirect()->route('login'); });
Route::get('/pricing', [SubscriptionController::class, 'showSubscriptionPage'])->name('pricing');
require __DIR__.'/auth.php';


/*
|--------------------------------------------------------------------------
| Agent Routes
|--------------------------------------------------------------------------
*/
Route::prefix('agent')->name('agent.')->group(function () {
    // Add login check for all Agent URLS inside this block
    Route::middleware(['agent', 'verified'])->group(function () {
        Route::get('/', [AgentsController::class, 'agents'])->name('index');
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('welcome', [DashboardController::class,  'welcome' ])->name('welcome');
        Route::get('notifications', [DashboardController::class, 'notifications'])->name('notifications');

         /*
        |--------------------------------------------------------------------------
        | Property Module
        |--------------------------------------------------------------------------
        */
        Route::prefix('property')->name('property.')->group(function () {
            Route::get('address', [PropertyController::class, 'address'])->name('addresses');
            Route::get('address/{property_id}', [PropertyController::class, 'address'])->name('address');
            Route::get('amenities', [PropertyController::class, 'amenities'])->name('amenities');
            Route::get('description', [PropertyController::class, 'description'])->name('description');
            Route::get('price-feature', [PropertyController::class, 'priceFeature'])->name('price_feature');
            Route::get('default', [PropertyController::class, 'default'])->name('default');
            Route::get('publish-property/{id}', [PropertyController::class, 'PublishProperty'])->name('publish');
            Route::get('delete/{id}', [PropertyController::class, 'deleteProperty'])->name('delete');
            Route::get('delete-property', [PropertyController::class, 'confirmDeleteProperty']);
            Route::get('listing', [PropertyController::class, 'listing'])->name('listing');
            Route::post('store-address', [PropertyController::class, 'storeAddress']);
            Route::post('store-address/{data}', [PropertyController::class, 'storeAddress'])->name('store_address');
            Route::post('store-amenities', [PropertyController::class, 'storeAmenities'])->name('store_amenities');
            Route::post('store-description', [PropertyController::class, 'storeDescription'])->name('store_description');
            Route::post('store-priceFeature', [PropertyController::class, 'storePriceFeature'])->name('store_price_feature');
        });

        /*
        |--------------------------------------------------------------------------
        | Property Images
        |--------------------------------------------------------------------------
        */
        Route::prefix('property-images')->group(function () {
            Route::post('save-images', [PropertyImagesController::class, 'storeImage']);
            Route::get('images', [PropertyImagesController::class, 'images']);
            Route::get('images/{id}', [PropertyImagesController::class, 'images']);
            Route::get('delete-images/{id}', [PropertyImagesController::class, 'daleteImage']);
            Route::get('rotate-images/{id}', [PropertyImagesController::class, 'rotateImage']);
        });


        /*
        |--------------------------------------------------------------------------
        | Galleries
        |--------------------------------------------------------------------------
        */
        Route::prefix('galleries')->name('galleries.')->group(function () {
            Route::get('gallery-images', [PropertyGalleriesController::class, 'galleryImages']);
            Route::get('default-gallery-images', [PropertyGalleriesController::class, 'DefaultGalleryImages'])->name('default');
            Route::get('gallery-images/{id}', [PropertyGalleriesController::class, 'galleryImages']);
            Route::get('save_featured_gallery_images', [PropertyGalleriesController::class, 'SaveFeaturdGalleryImages']);
            Route::get('property-galleries', [PropertyGalleriesController::class, 'propertyGalleries']);
            Route::get('delete-property-galleries', [ PropertyGalleriesController::class, 'deletePropertyGalleries']);
        });


        /*
        |--------------------------------------------------------------------------
        | Property Documents
        |--------------------------------------------------------------------------
        */

        Route::prefix('property-document')->name('property_document.')->group(function () {
            Route::get('/', [PropertyDocumentsController::class, 'property_documents'])->name('index');
            Route::get('/save-docs', [PropertyDocumentsController::class, 'saveDocs'])->name('save_docs');
            Route::get('/delete-document/{id}', [PropertyDocumentsController::class, 'deleteDoc'])->name('delete_doc');
            Route::get('/edit-doc-name', [PropertyDocumentsController::class,'editDocName'])->name('edit_doc_name');
        });



        /*
        |--------------------------------------------------------------------------
        | Property Floorplans
        |--------------------------------------------------------------------------
        */

        Route::prefix('property-floorplan')->name('floorplan.')->group(function () {
            Route::get('/', [PropertyFloorplansController::class, 'property_floorplan'])->name('index');
            Route::get('save-floorplans', [PropertyFloorplansController::class, 'saveFloorplans']);
            Route::get('save-hotspot-data', [PropertyFloorplansController::class, 'saveAddHotspot']);
            Route::get('delete-floorplan/{id}', [PropertyFloorplansController::class, 'deleteFloorplan']);
            Route::get('delete-hotspot', [PropertyFloorplansController::class, 'deletehotspot']);
            Route::get('add-hotspot/{floorplan_id}', [PropertyFloorplansController::class, 'addHotspotNew']);
            Route::post('add-area/{floorplan_id}', [PropertyFloorplansController::class, 'addHotspotArea']);
            Route::post('remove-area/{floorplan_id}', [PropertyFloorplansController::class, 'removeHotspotArea']);
            Route::post('update-hotspot-image/{floorplan_id}', [PropertyFloorplansController::class, 'updateHotspotImage']);
            Route::get('property-image/thumb/{id}', [PropertyFloorplansController::class, 'getPropertyImage']);
        });


        /*
        |--------------------------------------------------------------------------
        | Videos
        |--------------------------------------------------------------------------
        */
        Route::prefix('video')->name('video.')->group(function () {
            Route::get('video', [VideoController::class, 'video'])->name('video');
            Route::get('3d-video', [VideoController::class, 'ThreeD_video'])->name('3d-tour');
            Route::post('save-video', [VideoController::class, 'saveVideo']);
            Route::post('save-url-video', [VideoController::class, 'Save_Url_Video']);
            Route::get('delete-video/{id}', [VideoController::class, 'deleteVideo']);
            Route::get('cover-video', [VideoController::class, 'coverVideo']);
            Route::get('feature-video', [VideoController::class, 'featureVideo']);
            Route::get('save-matterport-url', [VideoController::class, 'saveMatterportUrl']);
            Route::get('delete-matterport-url/{id}', [VideoController::class, 'deleteMatterportUrl']);
        });


        /*
        |--------------------------------------------------------------------------
        | Topbar Selection
        |--------------------------------------------------------------------------
        */
        Route::prefix('property-topbar')->group(function () {
            Route::get('image', [TopbarsController::class, 'image'])->name('image');
            Route::get('video', [TopbarsController::class, 'video'])->name('video');
            Route::get('slider', [\App\Http\Controllers\Agent\SliderController::class, 'index'])->name('slider');
            Route::get('feature-image', [TopbarsController::class, 'featureImage']);
            Route::get('feature-slider', [TopbarsController::class, 'featureSlider']);
            Route::get('selection-for-top', [TopbarsController::class, 'selectionForTop']);
        });


        /*
        |--------------------------------------------------------------------------
        | Property Address
        |--------------------------------------------------------------------------
        */
        Route::prefix('address')->group(function () {
            Route::get('address-map', [AddressController::class, 'addressMap'])->name('address');
            Route::get('update-address-map', [AddressController::class, 'updateAddressMap']);
        });


        /*
        |--------------------------------------------------------------------------
        | Profile & Billing
        |--------------------------------------------------------------------------
        */
        Route::get('profile', [AgentsController::class, 'profile'])->name('profile');
        Route::post('edit-profile-details', [AgentsController::class, 'editProfileDetails'])->name('edit_profile');
        Route::post('edit-profile-address', [AgentsController::class, 'editProfileAddress'])->name('edit_profile_address');
        Route::post('edit-profile-image', [AgentsController::class, 'editProfileImage'])->name('edit_profile_image');
        Route::post('add-social-media-profile', [AgentsController::class, 'AddSociaMediaProfile'])->name('add_social_media');
        Route::get('delete-profile-image', [AgentsController::class, 'deleteProfileImage'])->name('delete_profile_image');
        Route::post('edit-logo-image', [AgentsController::class, 'editLogoImage'])->name('edit_logo');
        Route::get('delete-logo-image', [AgentsController::class, 'deleteLogoImage'])->name('delete_logo');
        Route::get('change-password', [AgentsController::class, 'changePassword'])->name('change_password');
        Route::post('change-password', [AgentsController::class, 'savePassword'])->name('save_password');
        Route::get('credit-plans', [AgentsController::class, 'credit_plans'])->name('credit_plans');
        Route::get('payment-success', [PaymentsController::class, 'paymentSuccess'])->name('payment_success');
        Route::get('payment-error', [PaymentsController::class, 'paymentError'])->name('payment_error');
        Route::post('stripe-checkout', [PaymentsController::class, 'payment'])->name('stripe.checkout');
        Route::get('billing', [AgentsController::class, 'billing'])->name('billing');
    });

    Route::get('agents-address', [AgentsController::class, 'agents_address'])->name('agents_address');
    Route::post('agents-address-add', [AgentsController::class, 'agents_address_add'])->name('agents_address_add');
});

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('terms-and-conditions', [HomeController::class, 'termsAndConditions'])->name('termsAndConditions');
Route::group(['prefix' => 'webhook'], function () {
    Route::post('/stripe', [StripeWebhookController::class, 'receiveWebhook'])->name('stripe');
});

/*
|--------------------------------------------------------------------------
| Public Property Detail Routes
|--------------------------------------------------------------------------
*/

Route::get('property-view', [PublicPropertyController::class, 'Contact_Form'])->name('Contact_Form');
Route::get('share/{unique_url}', [PublicPropertyController::class, 'shareProperty'])->name('shareProperty');

/*
|--------------------------------------------------------------------------
| Catch All Route - Must Stay Last
|--------------------------------------------------------------------------
*/

Route::get('/{unique_url}', [PublicPropertyController::class, 'details'])->name('details');
