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
        $this->app->singleton('AcornFavicons', function () {
            return new AcornFavicons($this->app, $this->app['config']['acorn-favicons']);
        });

        $this->mergeConfigFrom(
            __DIR__.'/../../config/acorn-favicons.php',
            'acorn-favicons'
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
            __DIR__.'/../../config/acorn-favicons.php' => $this->app->configPath('acorn-favicons.php'),
        ], 'config');

        $this->app->make('AcornFavicons');
    }
}
