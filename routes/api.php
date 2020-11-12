<?php

use Illuminate\Http\Request;
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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::prefix('auth')->group(function () {
    // public facing non authenticated routes
    Route::post('register', 'AuthController@register');
    Route::post('login', 'AuthController@login');
    Route::get('refresh', 'AuthController@refresh');    
    // authenticated routes
    Route::group(['middleware' => 'auth:api'], function() {
        Route::get('categories', 'CategoryController@getCategories');
        Route::get('inventories', 'InventoryController@getInventories');
        Route::post('logout', 'AuthController@logout');
        // admin allowed route
        Route::group(['middleware' => 'isAdmin'], function() {
            // categories route
            Route::post('categories', 'CategoryController@createCategory');
            Route::put('categories', 'CategoryController@updateCategory');
            Route::delete('categories', 'CategoryController@deleteCategory');
            // inventory routes
            Route::post('inventories', 'InventoryController@createInventory');
            Route::put('inventories', 'InventoryController@updateInventory');
            Route::delete('inventories', 'InventoryController@deleteInventory');
        });
        // customer allowed route
        Route::group(['middleware' => 'isCustomer'], function() {

        });
        // delivery allowed route
        Route::group(['middleware' => 'isDelivery'], function() {

        });
        Route::post('logout', 'AuthController@logout');
    });
});
