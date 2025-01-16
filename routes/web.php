<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RestaurantController;

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

    Route::resource('restaurants', RestaurantController::class)->except(['index', 'create', 'edit']);
    Route::get('restaurants', [RestaurantController::class, 'index'])->name('restaurants.index');
    Route::get('restaurants/{restaurant}', [RestaurantController::class, 'show'])->name('restaurants.show');
    Route::get('restaurants/create', [RestaurantController::class, 'create'])->name('restaurants.create');
    
    Route::get('restaurants/edit/{id}', [RestaurantController::class, 'edit'])->name('restaurants.edit');
    Route::get('restaurants/update', [RestaurantController::class, 'update'])->name('restaurants.update');
    Route::get('restaurants/destroy', [RestaurantController::class, 'destroy'])->name('restaurants.destroy');
});