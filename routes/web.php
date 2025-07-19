<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::controller(HomeController::class)->group(function () {
    Route::get('dashboard', 'index')->name('dashboard');
    Route::get('dashboard1', 'index1')->name('dashboard');

});
Route::controller(LoginController::class)->group(function () {
    Route::get('login', 'index')->name('login');
    Route::get('recoveryPassword', 'recoveryPassword')->name('recoveryPassword');
    Route::get('register', 'register')->name('register');
});
