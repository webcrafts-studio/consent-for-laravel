<?php

namespace WebcraftsStudio\ConsentForLaravel\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use WebcraftsStudio\ConsentForLaravel\ConsentForLaravelServiceProvider;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            ConsentForLaravelServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('app.key', 'base64:'.base64_encode(random_bytes(32)));
        $app['config']->set('cache.default', 'array');
        $app['config']->set('session.driver', 'array');
    }
}
