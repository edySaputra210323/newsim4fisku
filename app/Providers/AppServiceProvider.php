<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;
// use Filament\Support\Facades\FilamentAsset;
// use Filament\Support\Assets\Js;
// use Filament\Support\Assets\Css;


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
        // FilamentAsset::register([
        //     Css::make('custom-styles', asset('css/custom.css')),
        //     Js::make('custom-scripts', asset('js/custom.js')),
        // ]);
        Carbon::setLocale('id');
    }

}
