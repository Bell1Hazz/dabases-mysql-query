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

Route::get('/', function () {
    return redirect()->route('articles.index');
});

// ===== PUBLIC ROUTES =====

Route::get('/articles', [ArticleController::class, 'index'])->name('articles.index');
Route::get('/articles/{article}', [ArticleController::class, 'show'])->name('articles.show');

// ===== AUTHENTICATED ROUTES (Public - for Authors) =====

Route::middleware(['auth'])->group(function () {
    // Article Edit (for Authors to edit own articles)
    Route::get('/articles/{article}/edit', [ArticleController::class, 'edit'])->name('articles.edit');
    Route::put('/articles/{article}', [ArticleController::class, 'update'])->name('articles.update');
    
    // Article Create (for Authors)
    Route::get('/articles/create', [ArticleController::class, 'create'])->name('articles.create');
    Route::post('/articles', [ArticleController::class, 'store'])->name('articles.store');
});

// Comments
Route::post('articles/{article}/comments', [CommentController::class, 'store'])
    ->name('articles.comments.store');
Route::delete('articles/{article}/comments/{comment}', [CommentController::class, 'destroy'])
    ->name('articles.comments.destroy');

// Authors
Route::resource('authors', AuthorController::class)->only(['index', 'show']);

// ===== AUTH ROUTES =====

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        $user = auth()->user();
        
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($user->role === 'author') {
            return redirect()->route('author.dashboard');
        } else {
            return redirect()->route('articles.index');
        }
    })->name('dashboard');
});

// ===== AUTHOR ROUTES =====

Route::prefix('author')
    ->middleware(['auth', \App\Http\Middleware\IsAuthor::class])
    ->group(function () {
    
    // Author Dashboard
    Route::get('/dashboard', function() {
        $user = auth()->user();
        $articles = $user->articles()->with(['category', 'tags'])->latest()->paginate(10);
        
        $stats = [
            'total_articles' => $user->articles()->count(),
            'total_views' => $user->articles()->sum('views'),
            'total_comments' => \App\Models\Comment::whereIn('article_id', $user->articles->pluck('id'))->count(),
        ];
        
        return view('author.dashboard', compact('articles', 'stats'));
    })->name('author.dashboard');
    
    // Author can also use public routes for create/edit
    // No need to duplicate here
});

// ===== ADMIN ROUTES =====

Route::prefix('admin')
    ->middleware(['auth', \App\Http\Middleware\IsAdmin::class])
    ->group(function () {
    
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    
    Route::resource('articles', AdminArticleController::class)->names([
        'index' => 'admin.articles.index',
        'create' => 'admin.articles.create',
        'store' => 'admin.articles.store',
        'show' => 'admin.articles.show',
        'edit' => 'admin.articles.edit',
        'update' => 'admin.articles.update',
        'destroy' => 'admin.articles.destroy',
    ]);
    
    Route::resource('users', AdminUserController::class)->names([
        'index' => 'admin.users.index',
        'create' => 'admin.users.create',
        'store' => 'admin.users.store',
        'show' => 'admin.users.show',
        'edit' => 'admin.users.edit',
        'update' => 'admin.users.update',
        'destroy' => 'admin.users.destroy',
    ]);
    
    Route::resource('categories', AdminCategoryController::class)->except(['show'])->names([
        'index' => 'admin.categories.index',
        'create' => 'admin.categories.create',
        'store' => 'admin.categories.store',
        'edit' => 'admin.categories.edit',
        'update' => 'admin.categories.update',
        'destroy' => 'admin.categories.destroy',
    ]);
    
    Route::resource('tags', AdminTagController::class)->except(['show'])->names([
        'index' => 'admin.tags.index',
        'create' => 'admin.tags.create',
        'store' => 'admin.tags.store',
        'edit' => 'admin.tags.edit',
        'update' => 'admin.tags.update',
        'destroy' => 'admin.tags.destroy',
    ]);
});

require __DIR__.'/auth.php';