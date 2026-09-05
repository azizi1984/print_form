<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Models\User;
use App\Enums\Status;

class AuthController extends Controller
{

    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
            'profile_id' => ['required', 'string'],
        ]);

        if (Auth::attempt([
            'username' => $credentials['username'], 
            'password' => $credentials['password'], 
            'profile_id' => $credentials['profile_id'],
            'status' => Status::Active->value
        ])) {
            
            $request->session()->regenerate();
 
            return redirect()->intended('dashboard'); 
        }
        
        return back()->withErrors([
            'username' => 'The provided credentials do not match our records.',
        ])->onlyInput('username');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout(); 

        $request->session()->invalidate(); 
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    public function autoLogin(Request $request): RedirectResponse
    {
        $token = $request->input('token');

    if (empty($token)) {
            return redirect('/login')->withErrors(['username' => 'Invalid or missing token.']);
        }

        $user = User::where('temporary_token', $token)->where('status', Status::Active->value)->first();

        if ($user) {
            Auth::login($user);
            $request->session()->regenerate();

            $user->temporary_token = null;
            $user->save();

            return redirect()->intended('dashboard');
        }
        
        return redirect('/login');
    }

}