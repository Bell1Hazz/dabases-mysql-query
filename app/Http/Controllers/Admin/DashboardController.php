<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\User;
use App\Models\Comment;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total_articles' => Article::count(),
            'total_authors' => User::where('role', 'author')->count(),
            'total_views' => Article::sum('views'),
            'total_comments' => Comment::count(),
        ];

        $recentArticles = Article::with(['user', 'category'])->latest()->limit(5)->get();
        $topAuthors = User::withCount('articles')->where('role', 'author')->orderBy('articles_count', 'desc')->limit(5)->get();

        return view('admin.dashboard', compact('stats', 'recentArticles', 'topAuthors'));
    }
}