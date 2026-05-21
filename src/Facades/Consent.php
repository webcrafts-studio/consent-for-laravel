<?php

namespace WebcraftsStudio\ConsentForLaravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \WebcraftsStudio\ConsentForLaravel\ConsentManager
 */
class Consent extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \WebcraftsStudio\ConsentForLaravel\ConsentManager::class;
    }
}
