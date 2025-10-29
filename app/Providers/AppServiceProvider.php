<?php

namespace App\Providers;

use App\Models\Category;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
public function boot(): void
{
    // Cache kategori selama 1 jam
    View::composer('*', function ($view) {
        $categories = Cache::remember('footer_categories', 3600, function () {
            return Category::select('name', 'slug')->limit(5)->get();
        });

        $view->with('footerCategories', $categories);
    });
}

}
