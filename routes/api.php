<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommandeController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\IngredientController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\PanierController;
use App\Models\Commande;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    //     return $request->user();
    // });
Route::middleware(['cors'])->group(function(){
    Route::post('/register',[AuthController::class,'register']);
    Route::post('/login',[AuthController::class,'login']);
    Route::get('/activate/{token}',[AuthController::class,'activateAccount']);
    // Route::post('/dosth',[AuthController::class,'dosth'])->middleware(['auth:sanctum','isActivated']);
    Route::post('/resetRequest',[AuthController::class,'resetPasswordRequest']);
    Route::post('/reset/{token}',[AuthController::class,'resetPassword']);
    
    Route::prefix('menu')->middleware(['auth:sanctum'])->group(function(){
        Route::get('/{name?}',[MenuController::class,'getAllMenus']);
        Route::middleware(['isAdmin'])->group(function(){
            Route::post('/add',[MenuController::class,'addMenu']);
            Route::put('/update/{name}',[MenuController::class,'updateMenu']);
            Route::delete('/delete/{name}',[MenuController::class,'deleteMenu']);
        });
    });
    
    Route::prefix('ingredient')->middleware(['auth:sanctum'])->group(function(){
        Route::get('/{name?}',[IngredientController::class,'getAllIngredients']);
        Route::get('menu/{menu_id}',[IngredientController::class,'getIngredientPerMenu']);
        Route::middleware(['isAdmin'])->group(function(){
            Route::post('/add',[IngredientController::class,'addIngredient']);
            Route::put('/update/{name}',[IngredientController::class,'editIngredient']);
            Route::delete('/delete/{name}',[IngredientController::class,'deleteIngredient']);
        });
    });
    
    Route::prefix('cart')->middleware(['auth:sanctum'])->group(function(){
        Route::get('/{id?}',[PanierController::class,'getCart']);
        Route::post('/add',[PanierController::class,'addToCart']);
        Route::put('/update/{id}',[PanierController::class,'updateCart']);
        Route::delete('delete/{id}',[PanierController::class,'deleteFromCart']);
        Route::post('/redeemPoints',[PanierController::class,'getFreeMenu']);
    });
    
    Route::prefix('coupon')->middleware(['auth:sanctum'])->group(function(){
        Route::get('/',[CouponController::class,'getCoupons']);
        Route::middleware(['isAdmin'])->group(function(){
            Route::post('/add',[CouponController::class,'addCoupon']);
            Route::delete('/delete/{name}',[CouponController::class,'deleteCoupon']);
        });
    });
    
    Route::prefix('commande')->middleware(['auth:sanctum'])->group(function(){
        Route::middleware('isAdmin')->get('{reply}/{id}',[CommandeController::class,'replyCommand']);
        Route::post('/make/{coup?}',[CommandeController::class,'makeCommande']);
        Route::get('/{id?}',[CommandeController::class,'getCommand']);
        Route::delete('/delete/{id}',[CommandeController::class,'deleteCommand']);
    });

    Route::prefix('address')->middleware(['auth:sanctum'])->group(function(){
        Route::post('/add',[AddressController::class,'addAddress']);
    });
});