<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Contracts\ProviderSyncServiceInterface;

class ProviderSyncCommand extends Command
{
    protected $signature = 'provider:sync';
    protected $description = 'Sync events from the external provider';

    public function handle(ProviderSyncServiceInterface $service): int
    {
        $this->info('Starting synchronization with the provider...');

        $count = $service->sync();

        if ($count === 0) {
            $this->warn('No new events were processed or an error occurred.');
        } else {
            $this->info("Synchronization completed: {$count} events processed/updated.");
        }

        return self::SUCCESS;
    }
}
