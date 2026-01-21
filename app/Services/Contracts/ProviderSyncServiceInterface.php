<?php

namespace App\Services\Contracts;

interface ProviderSyncServiceInterface
{
    /**
     * Fetch from provider, parse, filter online, store/update historical events.
     * Returns number of processed events.
     */
    public function sync(): int;

    /**
     * For testing/debug: fetch raw XML string
     */
    public function fetchRawXml(): string;
}
