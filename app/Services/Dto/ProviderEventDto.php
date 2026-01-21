<?php

namespace App\Services\Dto;

use DateTime;

class ProviderEventDto
{
    public function __construct(
        public readonly string $provider_id,
        public readonly string $title,
        public readonly string $sell_mode,
        public readonly ?DateTime $starts_at,
        public readonly ?DateTime $ends_at,
        public readonly array $zones,
        public readonly ?array $raw_plan = null
    ) {}

    /**
     * Crea desde un <plan> SimpleXMLElement
     */
    public static function fromPlanElement(\SimpleXMLElement $plan, string $title, string $sell_mode): self
    {
        $attrs = $plan->attributes();

        $zones = [];
        foreach ($plan->zone ?? [] as $zone) {
            $zAttrs = $zone->attributes();
            $zones[] = [
                'zone_id'   => (string) ($zAttrs['zone_id'] ?? ''),
                'name'      => (string) ($zAttrs['name'] ?? ''),
                'price'     => (float) ($zAttrs['price'] ?? 0.0),
                'capacity'  => (int) ($zAttrs['capacity'] ?? 0),
                'numbered'  => filter_var($zAttrs['numbered'] ?? 'false', FILTER_VALIDATE_BOOLEAN),
            ];
        }

        return new self(
            provider_id: (string) ($attrs['plan_id'] ?? ''),
            title: $title,
            sell_mode: $sell_mode,
            starts_at: self::parseDate((string) ($attrs['plan_start_date'] ?? null)),
            ends_at: self::parseDate((string) ($attrs['plan_end_date'] ?? null)),
            zones: $zones,
            raw_plan: json_decode(json_encode($plan), true)
        );
    }

    private static function parseDate(?string $dateStr): ?DateTime
    {
        if (!$dateStr) return null;
        try {
            return new DateTime($dateStr);
        } catch (\Exception) {
            return null;
        }
    }

    public function toDbArray(): array
    {
        return [
            'provider_id' => $this->provider_id,
            'title'       => $this->title,
            'sell_mode'   => $this->sell_mode,
            'starts_at'   => $this->starts_at?->format('Y-m-d H:i:s'),
            'ends_at'     => $this->ends_at?->format('Y-m-d H:i:s'),
            'zones'       => $this->zones,
            'raw_data'    => $this->raw_plan,
        ];
    }
}
