<?php

use App\Http\Controllers\API\AUTH\AuthController;
use App\Http\Controllers\API\ForgetPassword\ForgetPasswordController;
use App\Http\Controllers\API\GENERAL\AddressController;
use App\Http\Controllers\API\GENERAL\BannerController;
use App\Http\Controllers\API\GENERAL\BrandController;
use App\Http\Controllers\API\GENERAL\CartController;
use App\Http\Controllers\API\GENERAL\CategoryController;
use App\Http\Controllers\API\GENERAL\CenterController;
use App\Http\Controllers\API\GENERAL\CheckoutController;
use App\Http\Controllers\API\GENERAL\CouponController;
use App\Http\Controllers\API\GENERAL\FavoriteController;
use App\Http\Controllers\API\GENERAL\GovernorateController;
use App\Http\Controllers\API\GENERAL\OfferController;
use App\Http\Controllers\API\GENERAL\OrderController;
use App\Http\Controllers\API\GENERAL\PageController;
use App\Http\Controllers\API\GENERAL\ProductController;
use App\Http\Controllers\API\GENERAL\ProviderController;
use App\Http\Controllers\API\GENERAL\ReviewController;
use App\Http\Controllers\API\GENERAL\SettingController;
use App\Http\Middleware\serviceProviderOnly;
use App\Http\Middleware\SetLang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => SetLang::class], function () {
    // Brands Routes
    Route::get('brands', [BrandController::class, 'getBrands']);
    Route::get('brand', [BrandController::class, 'getBrand']);
    // Categories Routes
    Route::get('categories', [CategoryController::class, 'getCategories']);
    Route::get('category', [CategoryController::class, 'getCategory']);
    Route::get('sub-categories', [CategoryController::class, 'getSubCategories']);
    Route::get('sub-category', [CategoryController::class, 'getSubCategory']);
    Route::get('services/sub-category', [ProviderController::class, 'servicesSubCategory']);
    // Banners Routes
    Route::get('banners', [BannerController::class, 'getBanners']);
    // products Routes
    Route::get('products', [ProductController::class, 'getProducts']);
    Route::get('product', [ProductController::class, 'getProduct']);
    Route::get('products-filter', [ProductController::class, 'filter']);
    Route::get('products-search', [ProductController::class, 'search']);
    // Offers Routes
    Route::get('offers', [OfferController::class, 'getAllActiveOffer']);
    Route::get('offer', [OfferController::class, 'getOffer']);
    // Pages Routes
    Route::get('pages', [PageController::class, 'getPages']);
    // Services Routes
    Route::get('services', [ProviderController::class, 'services']);
    Route::get('service', [ProviderController::class, 'getService']);
    Route::post('services/book', [ProviderController::class, 'bookService'])->middleware('auth:sanctum');
    // Settings Routes
    Route::get('settings', [SettingController::class, 'getSettings']);
    // Governorates Routes
    Route::get('governorates', [GovernorateController::class, 'getAllGovernorates']);
    // Centers Routes
    Route::get('centers', [CenterController::class, 'getCenters']);

    // Auth Routes
    Route::post('register', [AuthController::class, 'register']);
    Route::post('verify-otp', [AuthController::class, 'verifyOtp']);
    Route::post('resend-otp', [AuthController::class, 'resendOtp']);
    Route::post('login', [AuthController::class, 'login']);

    // Social Login Redirect/Callback
    Route::get('auth/{provider}/redirect', [AuthController::class, 'redirectToProvider']);
    Route::get('auth/{provider}/callback', [AuthController::class, 'handleProviderCallback']);
    // Forget Password Routes
    Route::post('forget-send-otp', [ForgetPasswordController::class, 'sendOtp']);
    Route::post('forget-verify-otp', [ForgetPasswordController::class, 'verifyOtp']);
    Route::post('reset-password', [ForgetPasswordController::class, 'resetPassword']);
    Route::post('forget-resend-otp', [ForgetPasswordController::class, 'resendOtp']);
    Route::get('reviews/all/general/get', [ReviewController::class, 'getGeneralReviews']);
    // Authenticated Routes
    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::post('logout', [AuthController::class, 'logoutCurrentDevice']);
        Route::post('logout-all-devices', [AuthController::class, 'logoutAllDevices']);
        Route::get('show-profile', [AuthController::class, 'showProfile']);
        Route::post('update-profile', [AuthController::class, 'updateProfile']);
        Route::delete('delete-account', [AuthController::class, 'deleteAccount']);
        // Provider Routes
        Route::group(['middleware' => ['auth:sanctum', serviceProviderOnly::class]], function () {
            Route::get('provider-profile', [ProviderController::class, 'providerProfile']);
            Route::post('update-provider-profile', [ProviderController::class, 'updateProviderProfile']);
            Route::post('services/create', [ProviderController::class, 'createService']);
            Route::post('services/update', [ProviderController::class, 'updateService']);
            Route::post('services/delete', [ProviderController::class, 'deleteService']);
            
        });
        // Review Routes
        Route::post('reviews/product/create', [ReviewController::class, 'storeProductReview']);
        Route::post('reviews/general/create', [ReviewController::class, 'storeGeneralReview']);
        Route::get('reviews/product/get', [ReviewController::class, 'getProductReviews']);
        Route::get('reviews/general/get', [ReviewController::class, 'getMyGeneralReviews']);
        Route::put('reviews/update/product/{id}', [ReviewController::class, 'updateProductReview']);
        Route::put('reviews/update/general/{id}', [ReviewController::class, 'updateGeneralReview']);
        Route::delete('reviews/{id}', [ReviewController::class, 'deleteReview']);

        // Cart Routes
        Route::get('carts', [CartController::class, 'getCart']);
        Route::post('carts/add', [CartController::class, 'addToCart']);
        Route::put('carts/update', [CartController::class, 'updateQuantity']);
        Route::delete('carts/remove', [CartController::class, 'removeItem']);
        Route::delete('carts/clear', [CartController::class, 'clearCart']);
        Route::post('carts/checkout', [CheckoutController::class, 'checkout']);

        // Orders Routes
        Route::get('orders', [OrderController::class, 'getMyOrders']);
        Route::get('orders/search', [OrderController::class, 'searchOrders']);
        Route::get('orders/{id}', [OrderController::class, 'getOrderDetails']);
        Route::post('orders/{id}/cancel', [OrderController::class, 'cancelOrder']);
        Route::delete('orders/{id}/delete', [OrderController::class, 'deleteOrder']);

        // Favorite Routes
        Route::get('favorites', [FavoriteController::class, 'getFavorites']);
        Route::post('favorites/add', [FavoriteController::class, 'toggleFavorite']);
        Route::post('favorites/remove', [FavoriteController::class, 'toggleFavorite']);

        // Address Routes
        Route::get('addresses', [AddressController::class, 'getUserAddresses']);
        Route::post('addresses/create', [AddressController::class, 'store']);
        Route::post('addresses/update', [AddressController::class, 'updateAddress']);
        Route::delete('addresses/delete', [AddressController::class, 'deleteAddress']);
        Route::post('addresses/make-default', [AddressController::class, 'makeDefaultAddress']);
        // Coupons Routes
        Route::get('coupons', [CouponController::class, 'getCoupons']);
        Route::post('coupons/apply', [CouponController::class, 'applyCoupon']);
    });
});
