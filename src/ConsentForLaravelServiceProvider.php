<?php

namespace WebcraftsStudio\ConsentForLaravel;

use Illuminate\Support\Facades\Blade;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use WebcraftsStudio\ConsentForLaravel\Commands\InstallCommand;

class ConsentForLaravelServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('consent-for-laravel')
            ->hasConfigFile()
            ->hasViews()
            ->hasCommand(InstallCommand::class);
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(ConsentManager::class);
        $this->app->alias(ConsentManager::class, 'consent-for-laravel');
    }

    public function packageBooted(): void
    {
        Blade::if('consent', fn (string $category): bool => $this->app->make(ConsentManager::class)->has($category));
        Blade::if('unlessconsent', fn (string $category): bool => $this->app->make(ConsentManager::class)->missing($category));
    }
}
