<?php

    use App\Http\Controllers\AuthController;
    use Illuminate\Support\Facades\Route;

    Route::get('/', fn () => view('pages.ranks.index'));

    Route::middleware('guest')->group(function () {
        Route::get('/register', fn () => view('auth.register'))->name('register.form');
        Route::post('/register', [AuthController::class, 'register'])->name('register');

        Route::get('/login', fn () => view('auth.login'))->name('login.form');
        Route::post('/login', [AuthController::class, 'login'])->name('login');
    });

    Route::middleware('auth')->group(function () {
        Route::get('/ranks', fn () => view('pages.ranks.index'))->name('ranks.index');

        Route::resource('domains', \App\Http\Controllers\DomainController::class)->except(['show']);

        Route::post('/locations/refresh', [\App\Http\Controllers\LocationController::class, 'refreshFromService'])
            ->name('locations.refresh');
        Route::resource('locations', \App\Http\Controllers\LocationController::class)->except(['show']);

        Route::post('/languages/refresh', [\App\Http\Controllers\LanguageController::class, 'refreshFromService'])
            ->name('languages.refresh');
        Route::resource('languages', \App\Http\Controllers\LanguageController::class)->except(['show']);

        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    });