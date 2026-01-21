<?php

namespace App\Services;

use App\Repositories\Contracts\EventRepositoryInterface;
use App\Services\Contracts\ProviderSyncServiceInterface;
use App\Services\Dto\ProviderEventDto;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;
use SimpleXMLElement;
use Throwable;

class ProviderSyncService implements ProviderSyncServiceInterface
{
    private const PROVIDER_URL = 'https://provider.code-challenge.feverup.com/api/events';

    public function __construct(
        protected Client $httpClient,
        protected EventRepositoryInterface $eventRepository
    ) {}

    public function sync(): int
    {
        try {
            $xmlString = $this->fetchRawXml();
            $xml = simplexml_load_string($xmlString);

            if ($xml === false) {
                throw new \RuntimeException('Invalid XML from provider');
            }

            $processed = 0;

            foreach ($xml->output->base_plan ?? [] as $basePlan) {
                $attrs = $basePlan->attributes();
                $sellMode = (string) ($attrs['sell_mode'] ?? 'offline');
                $title = (string) ($attrs['title'] ?? '');

                if ($sellMode !== 'online') {
                    continue;
                }

                foreach ($basePlan->plan ?? [] as $plan) {
                    $dto = ProviderEventDto::fromPlanElement($plan, $title, $sellMode);

                    if (empty($dto->provider_id)) {
                        continue;
                    }

                    $this->eventRepository->upsertFromProvider($dto->toDbArray());
                    $processed++;
                }
            }

            Log::info("Provider sync completed: {$processed} events processed/updated.");
            return $processed;

        } catch (GuzzleException $e) {
            Log::error('Provider API request failed', ['exception' => $e->getMessage()]);
            return 0;
        } catch (Throwable $e) {
            Log::error('Provider sync error', ['exception' => $e->getMessage()]);
            return 0;
        }
    }

    public function fetchRawXml(): string
    {
        $response = $this->httpClient->get(self::PROVIDER_URL, [
            'timeout'         => 10,
            'connect_timeout' => 5,
            'headers'         => ['Accept' => 'application/xml'],
        ]);

        return $response->getBody()->getContents();
    }
}
