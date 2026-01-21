<?php

namespace App\Repositories;

use App\Models\Event;
use App\Repositories\Contracts\EventRepositoryInterface;
use Illuminate\Support\Collection;
use DateTimeInterface;

class EloquentEventRepository implements EventRepositoryInterface
{
    public function __construct(protected Event $model) {}

    public function findInDateRange(
        DateTimeInterface $startsAt,
        DateTimeInterface $endsAt
    ): Collection {
        return $this->model
            ->where('sell_mode', 'online')
            ->where(function ($query) use ($startsAt, $endsAt) {
                $query->whereBetween('starts_at', [$startsAt, $endsAt])
                    ->orWhereBetween('ends_at', [$startsAt, $endsAt])
                    ->orWhere(function ($q) use ($startsAt, $endsAt) {
                        $q->where('starts_at', '<=', $startsAt)
                            ->where('ends_at', '>=', $endsAt);
                    });
            })
            ->orderBy('starts_at')
            ->get();
    }

    public function upsertFromProvider(array $data): bool
    {
        return (bool) $this->model->updateOrCreate(
            ['provider_id' => $data['provider_id']],
            [
                'title'       => $data['title'],
                'sell_mode'   => $data['sell_mode'],
                'starts_at'   => $data['starts_at'],
                'ends_at'     => $data['ends_at'],
                'zones'       => $data['zones'],
                'raw_data'    => $data['raw_data'] ?? null,
                'last_seen_at' => now(),
            ]
        );
    }

    public function all(): Collection
    {
        return $this->model->all();
    }
}
