<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use App\Models\Category;
use Illuminate\Support\Facades\URL;

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
        if (request()->server('HTTP_X_FORWARDED_PROTO') === 'https') {
            URL::forceScheme('https');
        }
        View::composer('*', function ($view) {
            $navCategories = Cache::remember('nav_categories_full', 600, function () {
                return Category::query()
                    ->with([
                        'photos' => fn($q) => $q->where('is_primary', true)->latest()->limit(1),
                        'products' => fn($q) => $q->with([
                            'photos',
                        ])->where('active', true)
                        ->orderBy('sort_order')->orderBy('id'),
                    ])
                    ->orderBy('sort_order')->orderBy('id')
                    ->get();
            });

            $view->with('navCategories', $navCategories);
        });
    }
}
