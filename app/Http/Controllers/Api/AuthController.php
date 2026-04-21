<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\ApplicationPartner;
use App\Enums\Status;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    // 1. Login ด้วย Application ID & Token (Secret)
    public function login(Request $request)
    {
        $request->validate([
            'app_code' => 'required',
            'token' => 'required',
        ]);

        $partner = ApplicationPartner::where('application_id', $request->app_code)->first();

        if (!$partner) {
            return response()->json(['message' => 'Application Partner not found'], 200);
        }

        if ($request->token != $partner->token) {
            return response()->json(['message' => 'Invalid credentials (token mismatch)'], 401);
        }

        $partner->tokens()->delete();

        $accessToken = $partner->createToken('Partner API Token')->plainTextToken;

        return response()->json([
            'status' => Status::Active->value,
            'message' => 'Login success',
            'application_name' => $partner->application_name,
            'access_token' => $accessToken,
            'token_type' => 'Bearer',
            'expires_in' => config('sanctum.expiration') . ' minutes',
        ]);
    }

    // 2. Refresh Token
    public function refreshToken(Request $request)
    {
        // Debugging Token Resolution
        $tokenStr = $request->bearerToken();
        if (!$tokenStr) {
             return response()->json(['message' => 'No Bearer Token sent'], 401);
        }
        
        // Manually find the token
        $accessToken = \Laravel\Sanctum\PersonalAccessToken::findToken($tokenStr);
        if (!$accessToken) {
             return response()->json(['message' => 'Invalid Token'], 401);
        }
        
        // Try to load the user
        $user = $accessToken->tokenable;
        
        if (!$user) {
             return response()->json([
                 'message' => 'Token found but User (Partner) could not be loaded',
                 'tokenable_type' => $accessToken->tokenable_type,
                 'tokenable_id' => $accessToken->tokenable_id,
                 'debug_primary_key_of_model' => (new \App\Models\ApplicationPartner)->getKeyName(),
             ], 500);
        }
        

        $accessToken->delete();
        // Or if $request->user() works now:
        // $request->user()->currentAccessToken()->delete();

        // Create new token
        $newToken = $user->createToken('Partner API Token Refreshed')->plainTextToken;

        return response()->json([
            'message' => 'Token refreshed',
            'access_token' => $newToken,
            'token_type' => 'Bearer',
            'expires_in' => config('sanctum.expiration') . ' minutes',
        ]);
    }

    // 3. Logout
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }



    public function checkLogin(Request $request)
    {

        $request->validate([
            'username' => 'required',
            'password' => 'required',
            'profile_id' => 'required',
        ]);


        if (Auth::attempt([
            'username' => $request['username'], 
            'password' => $request['password'], 
            'profile_id' => $request['profile_id'],
            'status' => Status::Active->value
        ])) {
            $user = Auth::user();
            // $token = $user->createToken('Token Passport')->plainTextToken;
            $token = Hash::make(Str::random(125));
            
            $user->temporary_token = $token;
            $user->save();

            return response()->json([
                'status' => 'success',
                'message' => 'verify successful',
                'access_token' => $token,
            ]);
        }
            
        return response()->json([
            'status' => 'error',
            'message' => 'Username หรือ Password ไม่ถูกต้อง'
        ], 401);
    }

}