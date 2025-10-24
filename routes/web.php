<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\AuthorController;

// Home
Route::get('/', function () {
    return redirect()->route('articles.index');
});

// ===== PUBLIC ROUTES (No Auth) =====

// Articles (Read Only)
Route::get('/articles', [ArticleController::class, 'index'])->name('articles.index');
Route::get('/articles/{article}', [ArticleController::class, 'show'])->name('articles.show');

// Comment Routes
Route::post('articles/{article}/comments', [CommentController::class, 'store'])
    ->name('articles.comments.store');
Route::delete('articles/{article}/comments/{comment}', [CommentController::class, 'destroy'])
    ->name('articles.comments.destroy');

// Authors
Route::resource('authors', AuthorController::class)->only(['index', 'show']);

// ===== AUTH ROUTES =====

// Dashboard (redirect based on role)
Route::get('/dashboard', function () {
    if (auth()->check() && auth()->user()->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('articles.index');
})->middleware(['auth'])->name('dashboard');

// ===== ADMIN ROUTES (Auth + Admin Only) =====

Route::prefix('admin')->middleware(['auth'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', function() {
        return view('admin.dashboard', [
            'stats' => [
                'total_articles' => \App\Models\Article::count(),
                'total_authors' => \App\Models\User::where('role', 'author')->count(),
                'total_views' => \App\Models\Article::sum('views'),
                'total_comments' => \App\Models\Comment::count(),
            ],
            'recentArticles' => \App\Models\Article::with(['user', 'category'])->latest()->limit(5)->get(),
            'topAuthors' => \App\Models\User::withCount('articles')->where('role', 'author')->orderBy('articles_count', 'desc')->limit(5)->get(),
        ]);
    })->name('admin.dashboard');
    
    // Article CRUD (will be created next)
    Route::resource('articles', ArticleController::class)->except(['index', 'show'])->names([
        'create' => 'admin.articles.create',
        'store' => 'admin.articles.store',
        'edit' => 'admin.articles.edit',
        'update' => 'admin.articles.update',
        'destroy' => 'admin.articles.destroy',
    ]);
    
    // User Management (placeholder)
    Route::get('/users', function() {
        $users = \App\Models\User::withCount('articles')->latest()->paginate(10);
        return view('admin.users.index', compact('users'));
    })->name('admin.users.index');
    
    Route::get('/categories', function() {
        return 'Categories Management (Coming Soon)';
    })->name('admin.categories.index');
    
    Route::get('/tags', function() {
        return 'Tags Management (Coming Soon)';
    })->name('admin.tags.index');
});

// Breeze Auth Routes
require __DIR__.'/auth.php';