<?php

namespace StephenAsare\Paystack\Console;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    protected $signature = 'install:paystack';

    protected $description = 'Install the Paystack package';

    public function handle()
    {
        $this->info('Installing Paystack Package...');

        $this->comment('Publishing Configuration...');
        $this->call('vendor:publish', [
            '--tag' => 'paystack-config',
        ]);

        if ($this->confirm('Do you wish to publish the migrations (required for Subscriptions)?')) {
            $this->comment('Publishing Migrations...');
            $this->call('vendor:publish', [
                '--tag' => 'paystack-migrations',
            ]);
        }

        $this->info('Paystack Package Installed Successfully.');
    }
}
