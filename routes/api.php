<?php

use App\Http\Controllers\API\AUTH\AuthController;
use App\Http\Controllers\API\ForgetPassword\ForgetPasswordController;
use App\Http\Controllers\API\GENERAL\BrandController;
use App\Http\Controllers\API\GENERAL\CategoryController;
use App\Http\Controllers\API\GENERAL\PageController;
use App\Http\Controllers\API\GENERAL\ProductController;
use App\Http\Controllers\API\GENERAL\SettingController;
use App\Http\Controllers\API\GENERAL\CartController;
use App\Http\Middleware\SetLang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::group(["middleware"=>SetLang::class],function(){
    //Brands Routes
    Route::get("brands",[BrandController::class,"getBrands"]);
    Route::get("brand",[BrandController::class,"getBrand"]);
    //Categories Routes
    Route::get("categories",[CategoryController::class,"getCategories"]);
    Route::get("category",[CategoryController::class,"getCategory"]);
    Route::get("sub-categories",[CategoryController::class,"getSubCategories"]);
    Route::get("sub-category",[CategoryController::class,"getSubCategory"]);
    //products Routes
    Route::get("products",[ProductController::class,"getProducts"]);
    Route::get("product",[ProductController::class,"getProduct"]);
    //Pages Routes
    Route::get("pages",[PageController::class,"getPages"]);
    //Settings Routes
    Route::get("settings",[SettingController::class,"getSettings"]);
    // Auth Routes
    Route::post("register",[AuthController::class,"register"]);
    Route::post("verify-otp",[AuthController::class,"verifyOtp"]);
    Route::post("resend-otp",[AuthController::class,"resendOtp"]);
    Route::post("login",[AuthController::class,"login"]);
    
    // Social Login Redirect/Callback
    Route::get("auth/{provider}/redirect", [AuthController::class, "redirectToProvider"]);
    Route::get("auth/{provider}/callback", [AuthController::class, "handleProviderCallback"]);
    // Forget Password Routes
    Route::post("send-otp",[ForgetPasswordController::class,"sendOtp"]);
    Route::post("verify-otp",[ForgetPasswordController::class,"verifyOtp"]);
    Route::post("reset-password",[ForgetPasswordController::class,"resetPassword"]);
    Route::post("resend-otp",[ForgetPasswordController::class,"resendOtp"]);
    // Authenticated Routes
    Route::group(["middleware"=>"auth:sanctum"],function(){
        Route::post("logout",[AuthController::class,"logoutCurrentDevice"]);
        Route::post("logout-all-devices",[AuthController::class,"logoutAllDevices"]);
        Route::get("show-profile",[AuthController::class,"showProfile"]);
        Route::put("update-profile",[AuthController::class,"updateProfile"]);

        // Cart Routes
        Route::get("carts", [CartController::class, "getCart"]);
        Route::post("carts/add", [CartController::class, "addToCart"]);
        Route::put("carts/update", [CartController::class, "updateQuantity"]);
        Route::delete("carts/remove", [CartController::class, "removeItem"]);
        Route::delete("carts/clear", [CartController::class, "clearCart"]);
    });
});