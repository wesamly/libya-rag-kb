<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Admin\ArticleController;
use App\Http\Controllers\Api\Admin\CategoryController;
use App\Http\Controllers\Api\KnowledgeBaseController;
use App\Http\Controllers\Api\Admin\DashboardController;
use App\Http\Controllers\Api\Admin\ContentGapController;
use App\Http\Controllers\Api\Admin\ChatHistoryController;

// A single controller to handle all public data fetching
Route::controller(KnowledgeBaseController::class)->prefix('public')->group(function () {
    Route::get('/home', 'getHomeData');
    Route::get('/categories', 'getCategories');
    Route::get('/category/{slug}', 'getCategoryDetails');
    Route::get('/article/{slug}', 'getArticleDetails');
    Route::get('/search', 'getSearchResults');
});

// --- ADMIN API ROUTES ---
Route::middleware(['auth:sanctum'])->prefix('admin')->group(function () {
    
    // Get the authenticated user
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Dashboard
    Route::get('/stats', [DashboardController::class, 'getStats']);
    Route::get('/recent-activity', [DashboardController::class, 'getRecentActivity']);
    Route::post('/run-sync', [DashboardController::class, 'runSync']); // For the "Sync Now" button

    // Articles Data for Editor
    Route::get('/articles/editor-data', [ArticleController::class, 'getEditorData']);
    
    // Articles (Full CRUD)
    Route::apiResource('articles', ArticleController::class);
    
    // Categories (Full CRUD)
    Route::apiResource('categories', CategoryController::class);

    // AI / RAG Analytics
    Route::get('/chat-history', [ChatHistoryController::class, 'index']);
    Route::get('/chat-history/{id}', [ChatHistoryController::class, 'show']);
    Route::get('/content-gap/export', [ContentGapController::class, 'export']);
    Route::get('/content-gap', [ContentGapController::class, 'index']);
});