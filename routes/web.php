<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;


Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');


Route::get('/print-form', function () {
    return view('form.form');
});

Route::resource('roles', RoleController::class);

Route::middleware(['auth'])->group(function () {
    // dashboard
    Route::get('/dashboard', function () {
        return view('layouts.admin');
    })->name('dashboard');

});