<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ExportDeclarationController;


Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/redirect-login', [AuthController::class, 'autoLogin'])->name('auto-login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');


Route::get('/print-form', function () {
    return view('form.form');
});

Route::resource('roles', RoleController::class);

Route::middleware(['auth'])->group(function () {
    // dashboard
    Route::get('/dashboard', function () {
        return view('index');
    })->name('dashboard');

    Route::controller(ExportDeclarationController::class)->group(function () {
        Route::get('/ex-declaration', 'index')->name('ex-declaration');
        Route::get('/ex-declaration/create', 'create')->name('ex-declaration.create');
        Route::post('/ex-declaration', 'store')->name('ex-declaration.store');
        Route::get('/ex-declaration/{id}', 'show')->name('ex-declaration.show');
        Route::get('/ex-declaration/{id}/edit', 'edit')->name('ex-declaration.edit');
        Route::put('/ex-declaration/{id}', 'update')->name('ex-declaration.update');
        Route::delete('/ex-declaration/{id}', 'destroy')->name('ex-declaration.destroy');
    });

});