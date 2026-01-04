<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Http\Controllers\Api\AuthController;


Route::post('application/login', [AuthController::class, 'login']);


Route::middleware('auth:sanctum')->group(function () {

    //auth
    Route::post('application/refreshToken', [AuthController::class, 'refreshToken']);
    Route::post('application/logout', [AuthController::class, 'logout']);
    
    Route::get('application/test', function (Request $request) {
        return "API Test";
    });

    Route::get('application/generateToken', function () {
    $token = Str::random(125);
        return [
            'token' => $token,
            'hash' => Hash::make($token),
        ];
    });
});

Route::post('application/freeToken', function (Request $request) {
    $token = $request->input('value');
    return [
        'token' => $token,
        'hash' => Hash::make($token),
    ];
});
