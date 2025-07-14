<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::controller(HomeController::class)->group(function () {
    Route::get('admin-dashboard', 'index')->name('admin-dashboard');
});
Route::controller(LoginController::class)->group(function () {
    Route::get('login', 'index')->name('login');
    Route::get('recoveryPassord', 'recoveryPassord')->name('recoveryPassord');

});