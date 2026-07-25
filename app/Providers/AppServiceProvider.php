<?php

namespace App\Providers;

use App\Support\BrandAssetResolver;
use App\Support\HomePageContentResolver;
use App\Support\PublicTranslationResolver;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(BrandAssetResolver::class, fn () => new BrandAssetResolver());
        $this->app->singleton(HomePageContentResolver::class, fn () => new HomePageContentResolver());
        $this->app->singleton(PublicTranslationResolver::class, fn () => new PublicTranslationResolver());
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer(['layouts.public', 'auth.landing'], function ($view) {
            $view->with('brandAssets', app(BrandAssetResolver::class)->resolve());
        });
    }
}
