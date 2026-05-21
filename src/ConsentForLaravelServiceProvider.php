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
        Blade::directive('consent', function (string $expression): string {
            return "<?php if (app('".ConsentManager::class."')->has({$expression})): ?>";
        });

        Blade::directive('endconsent', fn (): string => '<?php endif; ?>');

        Blade::directive('unlessconsent', function (string $expression): string {
            return "<?php if (app('".ConsentManager::class."')->missing({$expression})): ?>";
        });

        Blade::directive('endunlessconsent', fn (): string => '<?php endif; ?>');
    }
}
