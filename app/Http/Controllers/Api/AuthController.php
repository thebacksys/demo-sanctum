<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
 
                // This is an API request (mobile)
                $user = User::where('email', $request->email)->first();
                $token = $user->createToken('mobile-token')->plainTextToken;
                return response()->json(['token' => $token], 200);
            
            
            // This is a web request
            // $request->session()->regenerate();
            // return redirect()->intended('dashboard');
        }

        return response()->json([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout(Request $request)
    {
        if ($request->wantsJson()) {
            // This is an API request (mobile)
            $request->user()->currentAccessToken()->delete();
            return response()->json(['message' => 'Logged out successfully'], 200);
        }

        // This is a web request
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
