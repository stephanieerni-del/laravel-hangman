<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\RegistrationController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('/verify')->group(function () {
    Route::name('verification.')->group(function () {
        Route::get('/', function () {
            return view('auth.verify-notice');
        })
            ->middleware('auth')
            ->name('notice');

        Route::get('/send', function (Request $request) {
            $request->user()->sendEmailVerificationNotification();

            return back()->with('message', 'Verification link sent!');
        })
            ->middleware(['auth', 'throttle:6,1'])
            ->name('send');

        Route::get('/{id}/{hash}', function (EmailVerificationRequest $request) {
            $request->fulfill();

            return redirect('/games');
        })
            ->middleware(['auth', 'signed'])
            ->name('verify');
    });
});

Route::middleware('guest')->group(function () {
    Route::name('registration.')->group(function () {
        Route::get('/registration', [RegistrationController::class, 'show'])->name('show');
        Route::post('/registration', [RegistrationController::class, 'save'])->name('save');
    });

    Route::get('/', [AuthController::class, 'show'])->name('login');
    Route::post('/', [AuthController::class, 'login'])->name('auth.login');

    Route::get('/oauth', [AuthController::class, 'oauthShow'])->name('oauth.show');
    Route::get('/oauth/callback', [AuthController::class, 'oauthLogin'])->name('oauth.login');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/logout', [AuthController::class, 'logout'])->name('auth.logout');
    Route::resource('games', GameController::class)->except(['edit'])->middleware(['verified']);
    Route::get('/leaderboard', [GameController::class, 'leaderboard'])->name('leaderboard')->middleware(['verified']);
});
