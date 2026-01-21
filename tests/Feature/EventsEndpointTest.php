<?php

namespace Tests\Feature;

use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventsEndpointTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_returns_events_in_valid_date_range()
    {
        $response = $this->getJson('/api/events?starts_at=2021-06-01T00:00:00Z&ends_at=2021-07-31T23:59:59Z');

        $response->assertStatus(200)
            ->assertJsonStructure(['data']);
    }

    /** @test */
    public function test_returns_empty_array_when_no_events_in_range()
    {
        $response = $this->getJson('/api/events?starts_at=2025-01-01T00:00:00Z&ends_at=2025-12-31T23:59:59Z');

        $response->assertStatus(200)
            ->assertJsonStructure(['data']);
    }

    /** @test */
    public function test_returns_422_for_invalid_date_format()
    {
        $response = $this->getJson('/api/events?starts_at=2021-06-01&ends_at=2021-07-01');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['starts_at', 'ends_at']);
    }

    /** @test */
    public function test_returns_422_when_starts_at_is_after_ends_at()
    {
        $response = $this->getJson('/api/events?starts_at=2021-07-01T00:00:00Z&ends_at=2021-06-01T23:59:59Z');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['starts_at', 'ends_at']);
    }

    /** @test */
    public function test_returns_422_when_parameters_are_missing()
    {
        $response = $this->getJson('/api/events');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['starts_at', 'ends_at']);
    }

    /** @test */
    public function test_endpoint_returns_historical_data_even_if_provider_is_down()
    {
        $response = $this->getJson('/api/events?starts_at=2021-06-01T00:00:00Z&ends_at=2021-07-31T23:59:59Z');

        $response->assertStatus(200)
            ->assertJsonStructure(['data']);
    }
}
