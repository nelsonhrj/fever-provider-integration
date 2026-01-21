<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;
use DateTimeInterface;

interface EventRepositoryInterface
{
    /**
     * Find events that overlap or are within the given date range
     * and have sell_mode = 'online'
     */
    public function findInDateRange(
        DateTimeInterface $startsAt,
        DateTimeInterface $endsAt
    ): Collection;

    /**
     * Upsert (update or insert) an event based on provider_id
     */
    public function upsertFromProvider(array $data): bool;

    /**
     * Optional: Get all events (for debugging or admin)
     */
    public function all(): Collection;
}
