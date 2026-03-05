<?php

    use App\Http\Controllers\AuthController;
    use Illuminate\Support\Facades\Route;

    Route::get('/', fn () => view('about'))->name('about');

    Route::middleware('guest')->group(function () {
        Route::get('/register', fn () => view('auth.register'))->name('register.form');
        Route::post('/register', [AuthController::class, 'register'])->name('register');

        Route::get('/login', fn () => view('auth.login'))->name('login.form');
        Route::post('/login', [AuthController::class, 'login'])->name('login');
    });

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

    Route::middleware('auth')->group(function () {
        Route::post('/ranks/{rank}/fetch-results', [\App\Http\Controllers\RankController::class, 'fetchResults'])
            ->name('ranks.fetch-results');
        Route::resource('/ranks', \App\Http\Controllers\RankController::class)->except(['edit', 'update']);

        Route::resource('domains', \App\Http\Controllers\DomainController::class)->except(['show']);

        Route::get('/locations/for-select', [\App\Http\Controllers\LocationController::class, 'getForSelect'])
            ->name('locations.for-select');
        Route::post('/locations/refresh', [\App\Http\Controllers\LocationController::class, 'refreshFromService'])
            ->name('locations.refresh');
        Route::resource('locations', \App\Http\Controllers\LocationController::class)->except(['show']);

        Route::get('/languages/for-select', [\App\Http\Controllers\LanguageController::class, 'getForSelect'])
            ->name('languages.for-select');
        Route::post('/languages/refresh', [\App\Http\Controllers\LanguageController::class, 'refreshFromService'])
            ->name('languages.refresh');
        Route::resource('languages', \App\Http\Controllers\LanguageController::class)->except(['show']);
    });