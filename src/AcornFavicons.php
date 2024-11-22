<?php

namespace ItinerisLtd\AcornFavicons;

use Illuminate\Support\Arr;
use Roots\Acorn\Application;

class AcornFavicons
{
    /**
     * The application instance.
     *
     * @var \Roots\Acorn\Application
     */
    protected $app;

    /**
     * Create a new Example instance.
     *
     * @param  \Roots\Acorn\Application  $app
     * @return void
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }
}
