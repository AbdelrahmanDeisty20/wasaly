<?php

use App\Http\Controllers\API\AUTH\AuthController;
use App\Http\Controllers\API\ForgetPassword\ForgetPasswordController;
use App\Http\Controllers\API\GENERAL\AddressController;
use App\Http\Controllers\API\GENERAL\BannerController;
use App\Http\Controllers\API\GENERAL\BookingController;
use App\Http\Controllers\API\GENERAL\BrandController;
use App\Http\Controllers\API\GENERAL\CartController;
use App\Http\Controllers\API\GENERAL\CategoryController;
use App\Http\Controllers\API\GENERAL\CenterController;
use App\Http\Controllers\API\GENERAL\CheckoutController;
use App\Http\Controllers\API\GENERAL\ContactController;
use App\Http\Controllers\API\GENERAL\CouponController;
use App\Http\Controllers\API\GENERAL\DayController;
use App\Http\Controllers\API\GENERAL\FavoriteController;
use App\Http\Controllers\API\GENERAL\GovernorateController;
use App\Http\Controllers\API\GENERAL\NotificationController;
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
    // Providers Routes
    Route::get('providers', [ProviderController::class, 'providers']);
    Route::get('providers/search', [ProviderController::class, 'searchProvider']);
    Route::get('providers/filter', [ProviderController::class, 'filterProvider']);
    Route::get('providers/get/{id}', [ProviderController::class, 'getProviderById']);

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

    //fcm token
    Route::post('fcm-token', [NotificationController::class, 'sendToken']);
    // test notifications
    Route::post('sendTestNotification',[NotificationController::class,'sendTestNotification']);
    Route::post('sendTestNotificationToUser',[NotificationController::class,'sendTestNotificationToUsers']);
    // days Routes
    Route::get('days', [DayController::class, 'getDays']);
    Route::get('times', [DayController::class, 'getTimes']);
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
            Route::delete('services/delete', [ProviderController::class, 'deleteService']);

            // Provider Products Routes
            Route::get('provider/products', [ProductController::class, 'getProviderProducts']);
            Route::post('products/create', [ProductController::class, 'createProduct']);
            Route::post('products/update', [ProductController::class, 'updateProduct']);
            Route::delete('products/delete', [ProductController::class, 'deleteProduct']);

            // Provider Product Orders Routes
            Route::get('provider/product-orders', [OrderController::class, 'getProviderOrders']);
            Route::put('provider/product-orders/status', [OrderController::class, 'updateOrderStatus']);
            Route::delete('provider/product-orders/delete', [OrderController::class, 'deleteProviderOrder']);

            // Provider Offers Routes
            Route::get('provider/offers', [OfferController::class, 'getProviderOffers']);
            Route::post('provider/offers/create', [OfferController::class, 'createOffer']);
            Route::post('provider/offers/update', [OfferController::class, 'updateOffer']);
            Route::delete('provider/offers/delete', [OfferController::class, 'deleteOffer']);

            Route::get('provider/bookings', [BookingController::class, 'providerBookings']);
            Route::put('provider/bookings/status', [BookingController::class, 'updateStatus']);
            
            // Provider Reschedule Routes
            Route::post('provider/booking/reschedule/propose', [BookingController::class, 'suggestReschedule']);
            Route::post('provider/booking/reschedule/accept', [BookingController::class, 'acceptReschedule']);
            Route::get('provider/booking/reschedule/requests', [BookingController::class, 'providerPendingReschedules']);
            Route::get('provider/booking/reschedule/my-proposals', [BookingController::class, 'providerMyProposals']);
        });
        // Review Routes
        Route::post('reviews/product/create', [ReviewController::class, 'storeProductReview']);
        Route::post('reviews/service/create', [ReviewController::class, 'storeServiceReview']);
        Route::post('reviews/provider/create', [ReviewController::class, 'storeProviderReview']);
        Route::post('reviews/general/create', [ReviewController::class, 'storeGeneralReview']);
        Route::get('reviews/product/get', [ReviewController::class, 'getProductReviews']);
        Route::get('reviews/service/get', [ReviewController::class, 'getServiceReviews']);
        Route::get('reviews/service/get/all', [ReviewController::class, 'getServiceReviewsall'])->middleware(serviceProviderOnly::class);
        Route::get('reviews/provider/get', [ReviewController::class, 'getProviderReviews']);
        Route::get('reviews/general/get', [ReviewController::class, 'getMyGeneralReviews']);
        Route::put('reviews/update/product/{id}', [ReviewController::class, 'updateProductReview']);
        Route::put('reviews/update/service', [ReviewController::class, 'updateServiceReview']);
        Route::put('reviews/update/provider', [ReviewController::class, 'updateProviderReview']);
        Route::put('reviews/update/general', [ReviewController::class, 'updateGeneralReview']);
        Route::delete('reviews/{id}', [ReviewController::class, 'deleteReview']);

        // Cart Routes
        Route::get('carts', [CartController::class, 'getCart']);
        Route::post('carts/add', [CartController::class, 'addToCart']);
        Route::put('carts/update', [CartController::class, 'updateQuantity']);
        Route::delete('carts/remove', [CartController::class, 'removeItem']);
        Route::delete('carts/clear', [CartController::class, 'clearCart']);
        Route::post('carts/checkout', [CheckoutController::class, 'checkout']);

        // Bookings Routes
        Route::get('my-bookings', [BookingController::class, 'myBookings']);
        Route::post('booking', [BookingController::class, 'bookService']);
        Route::post('booking/update', [BookingController::class, 'updateBooking']);
        Route::post('booking/cancel', [BookingController::class, 'cancelBooking']);
        Route::delete('booking/delete', [BookingController::class, 'deleteBooking']);

        // Customer Reschedule Routes
        Route::post('customer/booking/reschedule/propose', [BookingController::class, 'suggestReschedule']);
        Route::post('customer/booking/reschedule/accept', [BookingController::class, 'acceptReschedule']);
        Route::get('customer/booking/reschedule/requests', [BookingController::class, 'customerPendingReschedules']);
        Route::get('customer/booking/reschedule/my-proposals', [BookingController::class, 'customerMyProposals']);

        // Orders Routes
        Route::get('orders', [OrderController::class, 'getMyOrders']);
        Route::get('orders/search', [OrderController::class, 'searchOrders']);
        Route::get('orders/{id}', [OrderController::class, 'getOrderDetails']);
        Route::put('orders/{id}/update', [OrderController::class, 'updateOrder']);
        Route::post('orders/{id}/cancel', [OrderController::class, 'cancelOrder']);
        Route::delete('orders/{id}/delete', [OrderController::class, 'deleteOrder']);

        // Favorite Routes
        Route::get('favorites', [FavoriteController::class, 'getFavorites']);
        Route::get('favorites/service', [FavoriteController::class, 'getServiceFavorites']);
        Route::post('favorites/add', [FavoriteController::class, 'addFavorite']);
        Route::post('favorites/remove', [FavoriteController::class, 'removeFavorite']);
        Route::post('favorites/service/add', [FavoriteController::class, 'addServiceFavorite']);
        Route::post('favorites/service/remove', [FavoriteController::class, 'removeServiceFavorite']);

        // Address Routes
        Route::get('addresses', [AddressController::class, 'getUserAddresses']);
        Route::post('addresses/create', [AddressController::class, 'store']);
        Route::post('addresses/update', [AddressController::class, 'updateAddress']);
        Route::delete('addresses/delete', [AddressController::class, 'deleteAddress']);
        Route::post('addresses/make-default', [AddressController::class, 'makeDefaultAddress']);
        // Coupons Routes
        Route::get('coupons', [CouponController::class, 'getCoupons']);
        Route::post('coupons/apply', [CouponController::class, 'applyCoupon']);

        // Contacts Route
        Route::post('contact-us', [ContactController::class, 'store']);
        Route::get('contacts/my-contacts', [ContactController::class, 'myContacts']);

        // Notification Routes
        Route::get('notification/status', [NotificationController::class, 'NotificationStatus']);
        Route::post('notification/turn-on', [NotificationController::class, 'TurnOnNotification']);
        Route::post('notification/turn-off', [NotificationController::class, 'TurnOffNotification']);
        
        Route::get('notifications', [NotificationController::class, 'notifications']);
        Route::get('notifications/read/{id}', [NotificationController::class, 'readNotification']);
        Route::get('notifications/read-all', [NotificationController::class, 'readAllNotifications']);
        Route::get('notifications/delete/{id}', [NotificationController::class, 'deleteNotification']);
        Route::get('notifications/delete-all', [NotificationController::class, 'deleteAllNotifications']);

        // FCM Token Route
        Route::post('fcm-token-user', [NotificationController::class, 'sendToken']);
    });
});
