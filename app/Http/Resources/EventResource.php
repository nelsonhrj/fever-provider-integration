<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $zones = collect($this->zones ?? []);

        return [
            'id'          => $this->provider_id,
            'title'       => $this->title,
            'sell_mode'   => $this->sell_mode,
            'starts_at'   => $this->starts_at?->toIso8601ZuluString(),
            'ends_at'     => $this->ends_at?->toIso8601ZuluString(),
            'zones'       => $zones->map(function ($zone) {
                return [
                    'zone_id'   => $zone['zone_id'] ?? null,
                    'name'      => $zone['name'] ?? '',
                    'price'     => (float) ($zone['price'] ?? 0.0),
                    'capacity'  => (int) ($zone['capacity'] ?? 0),
                    'numbered'  => (bool) ($zone['numbered'] ?? false),
                ];
            })->values()->all(),
        ];
    }
}
