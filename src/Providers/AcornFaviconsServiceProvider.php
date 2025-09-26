<?php

declare(strict_types=1);

namespace ItinerisLtd\AcornFavicons\Providers;

use ItinerisLtd\AcornFavicons\AcornFavicons;
use Roots\Acorn\Sage\SageServiceProvider;

class AcornFaviconsServiceProvider extends SageServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(
            'AcornFavicons',
            fn (): AcornFavicons => new AcornFavicons($this->app, $this->app['config']['acorn-favicons']),
        );

        $this->mergeConfigFrom(
            __DIR__.'/../../config/favicons.php',
            'favicons'
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../../config/favicons.php' => $this->app->configPath('favicons.php'),
            __DIR__.'/../../config/favicons.json' => $this->app->configPath('favicons.json'),
        ], 'config');

        $this->app->make('AcornFavicons');
    }
}
