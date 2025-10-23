<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CommentController;

Route::get('/', function () {
    return redirect()->route('articles.index');
});

// Article Resource
Route::resource('articles', ArticleController::class);

// Nested Comment Resource
Route::resource('articles.comments', CommentController::class)->only(['store', 'destroy']);

// Author Resource
Route::resource('authors', AuthorController::class)->only(['index', 'show']);