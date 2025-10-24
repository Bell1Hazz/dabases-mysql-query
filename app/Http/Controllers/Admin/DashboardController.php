<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\User;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Comment;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total_articles' => Article::count(),
            'total_users' => User::count(),
            'total_authors' => User::where('role', 'author')->count(),
            'total_categories' => Category::count(),
            'total_tags' => Tag::count(),
            'total_comments' => Comment::count(),
            'total_views' => Article::sum('views'),
            'pending_comments' => Comment::where('is_approved', false)->count(),
        ];

        $recentArticles = Article::with(['user', 'category'])
                                ->latest()
                                ->limit(5)
                                ->get();

        $topArticles = Article::with(['user', 'category'])
                              ->orderBy('views', 'desc')
                              ->limit(5)
                              ->get();

        $topAuthors = User::withCount('articles')
                         ->where('role', 'author')
                         ->orderBy('articles_count', 'desc')
                         ->limit(5)
                         ->get();

        return view('admin.dashboard', compact('stats', 'recentArticles', 'topArticles', 'topAuthors'));
    }
}