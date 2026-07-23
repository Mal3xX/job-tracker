<?php

declare(strict_types=1);

use App\blogic\Admin\Controllers\AdminUserController;
use App\blogic\Accounts\Controllers\AccountController;
use App\blogic\Companies\Controllers\CompanyController;
use App\blogic\Companies\Controllers\ContactController;
use App\blogic\Applications\Controllers\ApplicationController;
use App\blogic\Dashboard\Controllers\DashboardController;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Middleware\ShareErrorsFromSession;

Route::prefix('v1')->group(function () {
    // Rotte pubbliche (login, register) con sessione attiva per poter autenticare
    Route::middleware([
        EncryptCookies::class,
        AddQueuedCookiesToResponse::class,
        StartSession::class,
        ShareErrorsFromSession::class,
        PreventRequestForgery::class,
    ])->group(function () {
        Route::post('auth/login', [AccountController::class, 'login'])->name('auth.login')->middleware('throttle:30,1');
        Route::post('auth/register', [AccountController::class, 'register'])->name('auth.register')->middleware('throttle:10,60');
    });

    // Rotte protette (middleware per session-based SPA auth)
    Route::middleware([
        EncryptCookies::class,
        AddQueuedCookiesToResponse::class,
        StartSession::class,
        ShareErrorsFromSession::class,
        PreventRequestForgery::class,
        'auth',
    ])->group(function () {

        // Auth
        Route::post('auth/logout', [AccountController::class, 'logout'])->name('auth.logout');
        Route::get('auth/me', [AccountController::class, 'me'])->name('auth.me');
        Route::post('auth/profile', [AccountController::class, 'updateProfile'])->name('auth.profile');
        
        // Admin
        Route::prefix('admin')->group(function () {
            Route::get('users', [AdminUserController::class, 'index'])->name('admin.users.index');
            Route::get('users/{id}', [AdminUserController::class, 'show'])->name('admin.users.show');
            Route::put('users/{id}', [AdminUserController::class, 'update'])->name('admin.users.update');
        });

        // Companies
        Route::get('companies/names', [CompanyController::class, 'names'])->name('companies.names');
        Route::apiResource('companies', CompanyController::class);

        // Contacts
        Route::get('companies/{company}/contacts', [ContactController::class, 'index'])->name('companies.contacts.index');
        Route::post('companies/{company}/contacts', [ContactController::class, 'store'])->name('companies.contacts.store');
        Route::put('contacts/{contact}', [ContactController::class, 'update'])->name('contacts.update');
        Route::delete('contacts/{contact}', [ContactController::class, 'destroy'])->name('contacts.destroy');

        // Applications
        Route::get('applications/platforms', [ApplicationController::class, 'platforms'])->name('applications.platforms');
        Route::get('applications/pending-companies', [ApplicationController::class, 'pendingCompanies'])->name('applications.pending-companies');
        Route::apiResource('applications', ApplicationController::class);

        // Dashboard
        Route::get('dashboard/stats', [DashboardController::class, 'stats'])->name('dashboard.stats');
    });
});
