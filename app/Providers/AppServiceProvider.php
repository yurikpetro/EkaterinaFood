<?php

namespace App\Providers;

use App\Services\CartService;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;
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
        App::setLocale(config('app.locale'));
        Carbon::setLocale(config('app.locale'));

        View::composer('layouts.app', function ($view): void {
            $cart = app(CartService::class);

            $view->with([
                'cartItems' => $cart->items(),
                'cartTotal' => $cart->total(),
                'cartCount' => $cart->count(),
            ]);
        });
    }
}
