<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CommentController;

Route::get('/', function () {
    return redirect()->route('articles.index');
});

// Resource Route for Article
Route::resource('articles', ArticleController::class);

Route::prefix('authors')->group(function () {
    Route::get('/', [AuthorController::class, 'index'])->name('authors.index');
    Route::get('/{user}', [AuthorController::class, 'show'])->name('authors.show');
});
// Comment Routes
Route::prefix('articles/{article}/comments')->group(function () {
    Route::post('/', [CommentController::class, 'store'])->name('comments.store');
    Route::delete('/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
});