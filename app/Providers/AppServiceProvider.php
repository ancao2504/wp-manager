<?php

namespace App\Providers;

use App\Http\Middleware\CheckPermissions;
use App\Services\JwtService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Routing\Router;
use App\Services\WordPressApiService;
use App\Services\WooCommerceApiService;
use App\Services\SeoAnalysisService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Đăng ký JwtService trước
        $this->app->singleton(JwtService::class, function ($app) {
            return new JwtService();
        });
        
        // Đăng ký các services
        $this->app->singleton(WordPressApiService::class, function ($app) {
            return new WordPressApiService($app->make(JwtService::class));
        });

        $this->app->singleton(WooCommerceApiService::class, function ($app) {
            return new WooCommerceApiService($app->make(JwtService::class));
        });

        $this->app->singleton(SeoAnalysisService::class, function ($app) {
            return new SeoAnalysisService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Đăng ký middleware
        $router = $this->app->make(Router::class);
        $router->aliasMiddleware('permission', CheckPermissions::class);

        // Đăng ký alias cho routes
        Route::pattern('site', '[0-9]+');
        Route::pattern('post', '[0-9]+');
        Route::pattern('product', '[0-9]+');
        Route::pattern('analysis', '[0-9]+');
    }
}
