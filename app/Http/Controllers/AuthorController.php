<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\View\View;

class AuthorController extends Controller
{
    /**
     * Display all authors (index)
     */
    public function index(): View
    {
        $authors = User::withCount('articles')
                      ->has('articles')
                      ->orderBy('articles_count', 'desc')
                      ->get();

        return view('authors.index', compact('authors'));
    }

    /**
     * Display specific author (show)
     */
    public function show(User $user): View
    {
        $user->load(['articles' => function ($query) {
            $query->latest('date')->with(['category', 'tags']);
        }]);

        $stats = [
            'total_articles' => $user->articles->count(),
            'total_views' => $user->articles->sum('views'),
            'avg_views' => $user->articles->count() > 0 
                ? round($user->articles->sum('views') / $user->articles->count())
                : 0,
            'categories_count' => $user->articles->pluck('category_id')->unique()->count(),
        ];

        return view('authors.show', compact('user', 'stats'));
    }
}