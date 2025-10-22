<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\View\View;

class AuthorController extends Controller
{
    /**
     * Display all authors with article count
     */
    public function index(): View
    {
        $authors = User::withCount('articles')
                      ->has('articles') // Only users with articles
                      ->orderBy('articles_count', 'desc')
                      ->get();

        return view('authors.index', compact('authors'));
    }

    /**
     * Display specific author profile
     */
    public function show(User $user): View
    {
        $user->load(['articles' => function ($query) {
            $query->latest('date')->with(['category', 'tags']);
        }]);

        $stats = [
            'total_articles' => $user->articles->count(),
            'total_views' => $user->articles->sum('views'),
            'avg_read_time' => $user->articles->avg(function ($article) {
                return (int) filter_var($article->read_time, FILTER_SANITIZE_NUMBER_INT);
            }),
            'categories_count' => $user->articles->pluck('category_id')->unique()->count(),
        ];

        return view('authors.show', compact('user', 'stats'));
    }
}