<?php

declare(strict_types=1);

namespace ItinerisLtd\AcornFavicons\Facades;

use ItinerisLtd\AcornFavicons\AcornFavicons as AcornFaviconsClass;
use Illuminate\Support\Facades\Facade;

class AcornFavicons extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return AcornFaviconsClass::class;
    }
}
