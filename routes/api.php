<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\IngredientController;
use App\Http\Controllers\MenuController;
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
    Route::middleware(['isAdmin'])->group(function(){
        Route::post('/add',[IngredientController::class,'addIngredient']);
        Route::put('/update/{name}',[IngredientController::class,'editIngredient']);
        Route::delete('/delete/{name}',[IngredientController::class,'deleteIngredient']);
    });
});