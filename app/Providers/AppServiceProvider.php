<?php

namespace App\Providers;

use App\Enums\ProductStatus;
use App\Enums\VerificationStatus;
use App\Models\Category;
use App\Models\Product;
use App\Models\SellerProfile;
use App\Models\Setting;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\View\View as ViewInstance;

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
        Paginator::defaultView('components.pagination');

        RateLimiter::for('login', function (Request $request): Limit {
            return Limit::perMinute(5)->by(Str::transliterate(Str::lower($request->string('email')).'|'.$request->ip()));
        });

        RateLimiter::for('registration', function (Request $request): Limit {
            return Limit::perHour(5)->by($request->ip());
        });

        View::composer(['layouts.*', 'public.*', 'auth.*'], function (ViewInstance $view): void {
            $view->with('settings', Setting::values());
        });

        View::composer('layouts.admin', function (ViewInstance $view): void {
            $view->with('queueCounts', [
                'sellers' => SellerProfile::query()->where('verification_status', VerificationStatus::Pending)->count(),
                'products' => Product::query()->where('status', ProductStatus::Pending)->count(),
            ]);
        });

        View::composer('layouts.public', function (ViewInstance $view): void {
            $view->with('navCategories', Category::query()->active()->ordered()->get(['id', 'name', 'slug', 'icon']));
        });
    }
}
