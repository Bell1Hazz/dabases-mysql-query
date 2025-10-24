<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login')
                ->with('error', 'Please login to access admin panel.');
        }

        if (!auth()->user()->isAdmin()) {
            return redirect()->route('articles.index')
                ->with('error', 'You do not have permission to access this area.');
        }

        return $next($request);
    }
}