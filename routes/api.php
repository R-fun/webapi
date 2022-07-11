<?php

use App\Http\Controllers\Api\BarangController;
use App\Http\Controllers\Api\BarangTokoController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\FeatureController;
use App\Http\Controllers\Api\JenisTokoController;
use App\Http\Controllers\Api\LoggingController;
use App\Http\Controllers\Api\LoginController;
// use App\Http\Controllers\Api\LogoutController;
use App\Http\Controllers\Api\Mobile\AuthController;
use App\Http\Controllers\Api\Mobile\ProductController;
use App\Http\Controllers\Api\Mobile\StoreController;
use App\Http\Controllers\Api\Mobile\VersioningController;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\SatuanController;
use App\Http\Controllers\Api\TokoController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\Mobile\UserController as UseMobileController;
use App\Models\Category;
// use Illuminate\Http\Request;
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
Route::post('/register',RegisterController::class);
Route::post('/login',LoginController::class);
Route::get("/v1/version", [VersioningController::class, "get"]);
Route::get("/v1/parsing", [VersioningController::class, "parsing"]);

Route::group(['prefix' => '/v1/auth'], function () {
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::post('forgot', [AuthController::class, 'forgot'])->name('forgot');
    Route::post('reset', [AuthController::class, 'reset'])->name('reset');
    Route::post('register', [AuthController::class, 'register'])->name('register');
    Route::post('reset-password', [UserController::class, 'resetPassword'])->name('resetPassword');
});

Route::group(['prefix' => '/v1', 'middleware' => ['jwthandle', 'verified']], function () {
    Route::post('store', [StoreController::class, 'store'])->name('store');
    Route::get('store/type', [StoreController::class, 'getStoreType'])->name('store.type');
});

Route::group(['prefix' => '/v1', 'middleware' => ['jwthandle', 'verified', 'auth.store']], function () {
    Route::get('product', [ProductController::class, 'get'])->name('product');
    Route::post('product', [ProductController::class, 'store'])->name('product');
    Route::post('product/retrieve', [ProductController::class, 'retrieve'])->name('product');
    Route::get('product/history', [ProductController::class, 'history'])->name('product');
    Route::delete('product/history', [ProductController::class, 'deleteHistory'])->name('product');
    Route::delete('product', [ProductController::class, 'delete'])->name('product');
    Route::put('product', [ProductController::class, 'update'])->name('product');
    Route::post('product/history', [ProductController::class, 'createHistory'])->name('product');
    Route::get('product/unit', [ProductController::class, 'getUnit'])->name('product');
    Route::get('product/category', [ProductController::class, 'getCategory'])->name('product');
    Route::get('product/chart', [ProductController::class, 'getReport'])->name('product');
    Route::get('product/explore', [ProductController::class, 'getExplore'])->name('product');

    Route::put('users/change-password', [UseMobileController::class, 'changePassword']);
    Route::put('users/change-profile', [UseMobileController::class, 'changeProfile']);
});

Route::middleware('jwthandle')->group(function(){
    // Route::get('/users',function(Request $request){
    //     return $request->user();
    // });
    Route::controller(UserController::class)->group(function(){
        Route::get('/users','getUser');
        Route::put('/users','updateprofile');
        Route::put('/user/password','updatepassword');
        Route::put('/admin/password','updatepasswordAdmin');
        Route::get('/user/{id}','getById');
        Route::delete('/user/{id}','erase');
        Route::post('/users','save');
    });
});


//toko Api
Route::controller(TokoController::class)->group(function(){
    Route::middleware('jwthandle')->group(function(){
        Route::post('/toko','save');
        Route::put('/toko/{id}','update');
        Route::delete('/toko/{id}','erase');
        Route::get('/toko','get');
        Route::get('/toko/{id}','getById');
        Route::get('/count/toko','getCountToko');
        Route::get('/order/toko','orderToko');
        Route::post('/toko/barang','getBarang');
        Route::post('/filter/toko','filtertoko');
    });
});

//barang Api
Route::controller(BarangController::class)->group(function(){
    Route::middleware('jwthandle')->group(function(){
        Route::post('/barang','save');
        Route::put('/barang/{id}','update');
        Route::delete('/barang/{id}','erase');
        Route::post('/barang/updateharga','updateHarga');
        Route::get('/barang','get');
        Route::get('/barang/{id}','getById');
        Route::get('/count/barang','getCountBarang');
        Route::get('/count/toko/{id}','getCountByToko');
        Route::get('/order/barang','orderBarang');
        Route::post('/filter/barang','filtertoko');
    });
});

Route::controller(SatuanController::class)->group(function(){
    Route::middleware('jwthandle')->group(function(){
        Route::post('/satuan','store');
        Route::put('/satuan/{id}','update');
        Route::delete('/satuan/{id}','delete');
        Route::get('/satuan','get');
	    Route::get('/satuan/{id}','retrieve');
    });
});

Route::controller(JenisTokoController::class)->group(function(){
    Route::middleware('jwthandle')->group(function(){
        Route::post('/jenistoko','save');
        Route::put('/jenistoko/{id}','update');
        Route::delete('/jenistoko/{id}','erase');
        Route::get('/jenistoko','get');
        Route::get('/jenistoko/{id}','getById');
    });
});

Route::controller(LoggingController::class)->group(function(){
    Route::middleware('jwthandle')->group(function(){
        Route::get('/log','get');
        Route::get('/log/{id}','getbyid');
        Route::post('/filter/log','filterlog');
        Route::get('/order/log','orderlog');
        Route::get('/export/log/{id}','exportlog');
        Route::post('/getdata/log/','getData');
    });
});

Route::controller(FeatureController::class)->group(function(){
    Route::middleware('jwthandle')->group(function(){
        Route::get('/compare/barang','compareFeature');
        Route::post('/compare','comparePrice');
    });
});

Route::controller(CategoryController::class)->group(function(){
        Route::middleware('jwthandle')->group(function(){
            Route::post('/category','save');
            Route::put('/category/{id}','update');
            Route::delete('/category/{id}','erase');
            Route::get('/category','get');
            Route::get('/category/{id}','getById');
        });
});

Route::controller(BarangTokoController::class)->group(function(){
        Route::middleware('jwthandle')->group(function(){
            Route::post('/barangtoko','save');
            Route::put('/barangtoko/{id}','update');
            Route::delete('/barangtoko/{id}','erase');
            Route::get('/barangtoko','get');
            Route::get('/barangtoko/{id}','getById');
        });
});
// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

