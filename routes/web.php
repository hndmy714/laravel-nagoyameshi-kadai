<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Admin\RestaurantController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\HomeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


require __DIR__.'/auth.php';

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => 'auth:admin'], function () {
    Route::get('home', [Admin\HomeController::class, 'index'])->name('home');
    Route::resource('users', Admin\UserController::class)->only(['index', 'show']);

    Route::resource('restaurants', Admin\RestaurantController::class);    
    Route::resource('company', Admin\CompanyController::class)->only(['index', 'edit', 'update']);

Route::resource('terms', Admin\TermController::class)->only(['index', 'edit', 'update']);
});

Route::prefix('admin')->name('admin.')->middleware('auth:admin')->group(function () {
    Route::resource('categories', Admin\CategoryController::class);
});

Route::group(['middleware' => 'guest:admin'], function () { 
    Route::get('/', [HomeController::class, 'index'])->name('home');
    
    Route::group(['middleware' => ['auth', 'verified']], function () {
        Route::resource('user',UserController::class)->only(['index', 'edit', 'update']);
    });
});
