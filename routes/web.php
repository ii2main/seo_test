<?php

    use App\Http\Controllers\AuthController;
    use Illuminate\Support\Facades\Route;

    Route::get('/', fn () => view('welcome'));

    Route::middleware('guest')->group(function () {
        Route::get('/register', fn () => view('auth.register'))->name('register.form');
        Route::post('/register', [AuthController::class, 'register'])->name('register');

        Route::get('/login', fn () => view('auth.login'))->name('login.form');
        Route::post('/login', [AuthController::class, 'login'])->name('login');
    });

    Route::middleware('auth')->group(function () {
        Route::get('/dashboard', fn () => view('dashboard'))->name('dashboard');
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    });