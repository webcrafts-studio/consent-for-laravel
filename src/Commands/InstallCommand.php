<?php

namespace WebcraftsStudio\ConsentForLaravel\Commands;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    public $signature = 'consent:install';

    public $description = 'Publish the Consent for Laravel config and views';

    public function handle(): int
    {
        $this->call('vendor:publish', [
            '--tag' => 'consent-for-laravel-config',
        ]);

        $this->call('vendor:publish', [
            '--tag' => 'consent-for-laravel-views',
        ]);

        $this->components->info('Consent for Laravel has been installed.');

        return self::SUCCESS;
    }
}
