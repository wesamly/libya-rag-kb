<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SpaController;
use App\Http\Controllers\Api\KnowledgeBaseController;
use App\Http\Controllers\Admin\SpaController as AdminSpaController;
use App\Http\Controllers\Auth\LoginController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- Chat Route ---
Route::post('/chat', [KnowledgeBaseController::class, 'handleChat'])->name('chat.submit');//->middleware('throttle:10,1'); // Limit to 10 requests per minute if needed

// --- Authentication Routes ---
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');


// --- Admin SPA (Protected) ---
Route::middleware(['auth:sanctum'])
    ->prefix('admin')
    ->group(function () {
        Route::get('/{any?}', [AdminSpaController::class, 'showAdminApp'])
            ->where('any', '.*')
            ->name('admin.index');
});


// --- Public SPA (Catch-All) ---
Route::get('/{any?}', [SpaController::class, 'showPublicApp'])
    ->where('any', '.*')
    ->name('public.index');
