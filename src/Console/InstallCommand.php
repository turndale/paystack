<?php

namespace Turndale\Paystack\Console;

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

        $this->info('Paystack config published successfully!');
    }
}
