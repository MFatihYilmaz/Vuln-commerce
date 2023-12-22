<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\apiController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:api')->get('/user', function () {
    return auth('api')->user();
});

Route::controller(apiController::class)->group(function (){
    Route::get('/users/all','getAllUsers');
    Route::post('/register',"register");
    Route::post('/login','login')->name('login');
    Route::post('changepass','changePass');
    Route::get('/users',"getUser");
    Route::get('/products/all','listproducts');
    Route::post('/addresses/{username}','setAddress');
    Route::get('/addresses/{username}','getAddress');
    Route::post('/basket','addBasket');
    Route::post('/basket/update','updateBasket');
    Route::get('/basket/clear','clearBasket');
    Route::get('/getcart','getBasket');
    Route::get('/orders/all','getAllOrders');
    Route::get('/orders/{id}','getOrdersDetail');
    Route::get('/products/{id}','getProductDetails');
    Route::get('/categories','getCategories');
    Route::get('/search/{search?}','search')->where('search', '(.*)');
    Route::get('/swagger.json','swagger');
    Route::post('/uploadFile','fileUpload');
    Route::get('/getfile','getFile');
    Route::post('/checkstock','checkStock');
    Route::post('/deposit/load','loadDeposit');
    Route::get('/users/remove/{user_id}','removeUser');
    Route::post('/ticket','getMessage');
    Route::post('/buy','buyProduct');
    Route::post('/resetpass','resetPass');
    Route::post('/coupon/add','couponCode');
    Route::get('/coupon/remove','removeCouponCode');
    
});
