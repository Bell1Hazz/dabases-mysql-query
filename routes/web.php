<?php

use App\Http\Controllers\ArticleController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('articles.index');
});

// Resource Route for Article
Route::resource('articles', ArticleController::class);

Route::prefix('authors')->group(function () {
    Route::get('/', [AuthorController::class, 'index'])->name('authors.index');
    Route::get('/{user}', [AuthorController::class, 'show'])->name('authors.show');
});
