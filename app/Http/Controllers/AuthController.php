<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginAuthRequest;
use App\Models\User;
use Auth;
use Exception;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function show()
    {
        return view('auth.show');
    }

    public function login(LoginAuthRequest $request)
    {
        $credentials = $request->safe(['name', 'password']);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->route('games.index');
        }

        return back()->withErrors([
            'name' => 'The email and password does not match.',
        ])->onlyInput('name');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function oauthShow()
    {
        $options = [
            'prompt' => 'select_account consent',
            // 'hd' => 'cvsu.edu.ph',
        ];

        return Socialite::driver('google')
            ->with($options)
            ->redirect();
    }

    public function oauthLogin()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $user = User::firstWhere('email', $googleUser->email);

            if (! $user) {
                $user = User::create([
                    'name' => $googleUser->email,
                    'email' => $googleUser->email,
                    'password' => '',
                    'email_verified_at' => now(),
                ]);
            }

            Auth::login($user);

            return redirect('/games');
        } catch (Exception) {
            return redirect()->route('login')->withErrors([
                'oauth' => 'Login via Google account failed. Please try again.',
            ]);
        }
    }
}
