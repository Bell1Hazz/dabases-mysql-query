<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AdminArticleController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminTagController;

// ============================================================
// PUBLIC ROUTES
// ============================================================

Route::get('/', function () {
    return redirect()->route('articles.index');
});

// Articles (public)
Route::get('/articles', [ArticleController::class, 'index'])->name('articles.index');
Route::get('/articles/{article}', [ArticleController::class, 'show'])->name('articles.show');

// Comments (public)
Route::post('articles/{article}/comments', [CommentController::class, 'store'])
    ->name('articles.comments.store');
Route::delete('articles/{article}/comments/{comment}', [CommentController::class, 'destroy'])
    ->name('articles.comments.destroy');

// Authors (public)
Route::resource('authors', AuthorController::class)->only(['index', 'show']);

// ============================================================
// AUTH ROUTES
// ============================================================

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return auth()->user()->role === 'admin'
            ? redirect()->route('admin.dashboard')
            : redirect()->route('articles.index');
    })->name('dashboard');
});

// ============================================================
// ADMIN ROUTES
// ============================================================

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', \App\Http\Middleware\IsAdmin::class])
    ->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Articles
    Route::resource('articles', AdminArticleController::class);

    // Users
    Route::resource('users', AdminUserController::class);

    // Categories
    Route::resource('categories', AdminCategoryController::class)->except(['show']);

    // Tags
    Route::resource('tags', AdminTagController::class)->except(['show']);
});

require __DIR__.'/auth.php';
