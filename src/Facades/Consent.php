<?php

namespace WebcraftsStudio\ConsentForLaravel\Facades;

use Illuminate\Support\Facades\Facade;
use WebcraftsStudio\ConsentForLaravel\ConsentManager;

/**
 * @see ConsentManager
 */
class Consent extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return ConsentManager::class;
    }
}
