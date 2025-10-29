<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use App\Models\Category;

class ViewServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Share categories ke semua view
        View::composer('*', function ($view) {
            $footerCategories = Cache::remember('footer_categories', 3600, function () {
                return Category::select(['id', 'name', 'slug'])
                              ->limit(5)
                              ->get();
            });
            
            $view->with('footerCategories', $footerCategories);
        });
    }
}