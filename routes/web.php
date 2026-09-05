<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ExportDeclarationController;
use App\Http\Controllers\HeaderTemplateController;
use App\Http\Controllers\DetailTemplateController;
use App\Http\Controllers\FooterTemplateController;

// Default entry point: redirect to dashboard if logged in, otherwise redirect to login
Route::get('/', function () {
    return Auth::check() ? redirect()->route('dashboard') : redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/redirect-login', [AuthController::class, 'autoLogin'])->name('auto-login');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/print-form', function () {
        return view('form.form');
    });
    Route::resource('roles', RoleController::class);
    Route::resource('permissions', PermissionController::class);
    Route::resource('users', UserController::class);
    // dashboard
    Route::get('/dashboard', function () {
        return view('index');
    })->name('dashboard');
    Route::prefix('ex-declaration')->group(function () {
        Route::controller(ExportDeclarationController::class)->group(function () {
            Route::get('/profile-template', 'index')->name('profile-template');
            Route::get('/profile-template/create', 'create')->name('profile-template.create');
            Route::post('/profile-template', 'store')->name('profile-template.store');
            Route::get('/profile-template/{id}', 'show')->name('profile-template.show');
            Route::get('/profile-template/{id}/edit', 'edit')->name('profile-template.edit');
            Route::put('/profile-template/{id}', 'update')->name('profile-template.update');
            Route::delete('/profile-template/{id}', 'destroy')->name('profile-template.destroy');
        });

        Route::controller(HeaderTemplateController::class)->group(function () {
            Route::get('/header-template', 'index')->name('header-template');
            Route::get('/header-template/create', 'create')->name('header-template.create');
            Route::post('/header-template', 'store')->name('header-template.store');
            Route::get('/header-template/{id}', 'show')->name('header-template.show');
            Route::get('/header-template/{id}/edit', 'edit')->name('header-template.edit');
            Route::put('/header-template/{id}', 'update')->name('header-template.update');
            Route::delete('/header-template/{id}', 'destroy')->name('header-template.destroy');
            Route::post('/header-template/{id}/copy', 'copy')->name('header-template.copy');
        });

        Route::controller(DetailTemplateController::class)->group(function () {
            Route::get('/detail-template', 'index')->name('detail-template');
            Route::get('/detail-template/create', 'create')->name('detail-template.create');
            Route::post('/detail-template', 'store')->name('detail-template.store');
            Route::get('/detail-template/{id}', 'show')->name('detail-template.show');
            Route::get('/detail-template/{id}/edit', 'edit')->name('detail-template.edit');
            Route::put('/detail-template/{id}', 'update')->name('detail-template.update');
            Route::delete('/detail-template/{id}', 'destroy')->name('detail-template.destroy');
            Route::post('/detail-template/{id}/copy', 'copy')->name('detail-template.copy');
        });

        Route::controller(FooterTemplateController::class)->group(function () {
            Route::get('/footer-template', 'index')->name('footer-template');
            Route::get('/footer-template/create', 'create')->name('footer-template.create');
            Route::post('/footer-template', 'store')->name('footer-template.store');
            Route::get('/footer-template/{id}', 'show')->name('footer-template.show');
            Route::get('/footer-template/{id}/edit', 'edit')->name('footer-template.edit');
            Route::put('/footer-template/{id}', 'update')->name('footer-template.update');
            Route::delete('/footer-template/{id}', 'destroy')->name('footer-template.destroy');
        });
    });
});