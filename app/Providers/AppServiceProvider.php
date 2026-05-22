<?php

namespace App\Providers;

use App\Support\Settings;
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

        foreach ([
            storage_path('app/public/products'),
            storage_path('app/public/livewire-tmp'),
        ] as $directory) {
            if (! is_dir($directory)) {
                mkdir($directory, 0755, true);
            }
        }

        View::composer('layouts.app', function ($view): void {
            $cart = app(CartService::class);

            $view->with([
                'cartItems' => $cart->items(),
                'cartTotal' => $cart->total(),
                'cartCount' => $cart->count(),
                'headerPhone' => Settings::get(Settings::PHONE),
            ]);
        });
    }
}
