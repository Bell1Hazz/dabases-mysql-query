<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAuthor
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login')
                ->with('error', 'Please login to access this page.');
        }

        if (!in_array(auth()->user()->role, ['author', 'admin'])) {
            return redirect()->route('articles.index')
                ->with('error', 'Access denied. Authors only.');
        }

        return $next($request);
    }
}