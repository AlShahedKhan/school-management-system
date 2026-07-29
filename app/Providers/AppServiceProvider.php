<?php

namespace App\Providers;

use App\Models\AdmissionStudent;
use App\Models\SchoolExamName;
use App\Observers\AdmissionStudentObserver;
use App\Observers\SchoolExamNameObserver;
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
        SchoolExamName::observe(SchoolExamNameObserver::class);
        AdmissionStudent::observe(AdmissionStudentObserver::class);

        View::composer(['layouts.public', 'auth.landing'], function ($view) {
            $view->with('brandAssets', app(BrandAssetResolver::class)->resolve());
        });
    }
}
