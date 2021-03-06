<?php

use Illuminate\Support\Facades\Route;

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

Route::group(['as' => 'front.'], function () {
    Route::get('/', [\App\Http\Controllers\Front\PageController::class, 'main'])->name('main');
    Route::get('/product-detail/{product:slug}', [\App\Http\Controllers\Front\PageController::class, 'productDetail'])->name('productDetail');
    Route::get('/category/{category:slug}', [\App\Http\Controllers\Front\PageController::class, 'productsByCategory'])->name('productsByCategory');


    Route::group(['middleware' => 'auth'], function () {
        Route::get('/cart', [\App\Http\Controllers\Front\PageController::class, 'cart'])->name('cart');
        Route::get('/dashboard', [\App\Http\Controllers\Front\PageController::class, 'dashboard'])->name('dashboard');
        Route::get('/profile', [\App\Http\Controllers\Front\PageController::class, 'profile'])->name('profile');
        Route::get('/favorites', [\App\Http\Controllers\Front\PageController::class, 'favorites'])->name('favorites');

        Route::resource('address', \App\Http\Controllers\Front\AddressController::class);

        Route::post('/profile-update', [\App\Http\Controllers\Front\UserController::class, 'updateUserData'])->name('updateUserData');
        Route::post('/password-update', [\App\Http\Controllers\Front\UserController::class, 'updateUserPassword'])->name('updateUserPassword');
    });
});

Route::group(['prefix' => 'panel', 'as' => 'panel.', 'middleware' => ['admin_check']], function () {
    Route::get('dashboard', [\App\Http\Controllers\Admin\PageController::class, 'dashboard'])->name('dashboard');
    Route::get('users', [\App\Http\Controllers\Admin\PageController::class, 'users'])->name('users');


    Route::resources([
        'category' => \App\Http\Controllers\Admin\CategoryController::class,
        'brand' => \App\Http\Controllers\Admin\BrandController::class,
        'product' => \App\Http\Controllers\Admin\ProductController::class,
        'product.product_property' => \App\Http\Controllers\Admin\ProductPropertyController::class,
    ], ['except' => ['show', 'destroy']]);

    Route::resource('productPropertyName', \App\Http\Controllers\Admin\ProductPropertyNameController::class);

    Route::get('/{product}/product-images', [\App\Http\Controllers\Admin\PageController::class, 'productImages'])->name('productImages');
    Route::get('/{product}/product-variants', [\App\Http\Controllers\Admin\PageController::class, 'productVariants'])->name('productVariants');
});
