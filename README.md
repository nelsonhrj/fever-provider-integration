![Laravel](https://img.shields.io/badge/Laravel-12.x-red?logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.5-blue?logo=php)

# Fever Provider Integration Challenge

Microservice to integrate events/plans from an external provider into the Fever marketplace.

**Key features:**
- Periodic sync from the provider XML API
- Stores historical events (remains available even if removed from provider)
- Efficient GET /api/events endpoint with date range filtering
- Only online plans (`sell_mode: "online"`)
- Fast responses using local DB (no external calls at runtime)

## Tech Stack

- PHP 8.5
- Laravel 12
- MySQL 9.5 (SQLite for quick local dev)
- Guzzle for HTTP requests
- SimpleXML for parsing provider XML

## Quick Start

```bash
# 1. Clone the repository
git clone https://github.com/nelsonhrj/fever-provider-integration.git

# 2. Enter the project
cd fever-provider-integration

# 3. Config the database connection
# create the .env file from .env.example and modify the database credentials: 
# DB_USERNAME=dbuser
# DB_DATABASE=dbname
# DB_PASSWORD=dbpassword

# 4. Install dependencies, generate key, migrate DB
make install
make migrate

# 5. Sync events from provider (manual first run)
make sync

# 6. Start the server
make serve
```

## Makefile Commands

```bash
# make help              # Show all available commands
# make install           # Composer install + key generation
# make migrate           # Run migrations
# make sync              # Sync events from provider
# make fresh             # Reset DB + migrate + sync + serve
# make logs              # Tail sync logs in real-time
# make clear             # Clear all caches
# make test              # Run feature tests (alias to PHPUnit)
```

## Running & Testing the Endpoint (Manual Verification)

```bash
# Valid range (returns events if synced)
# curl "http://localhost:8000/api/events?starts_at=2021-06-01T00:00:00Z&ends_at=2021-07-31T23:59:59Z"

# Invalid format (422 JSON validation errors)
# curl "http://localhost:8000/api/events?starts_at=2021-06-01&ends_at=2021-07-01"

# Missing parameters (422)
# curl "http://localhost:8000/api/events"

# Future range (empty array)
# curl "http://localhost:8000/api/events?starts_at=2025-01-01T00:00:00Z&ends_at=2025-12-31T23:59:59Z"
```

Note: Responses are wrapped in {"data": [...]} when using JsonResource::collection(). All manual tests account for this.

## Automatic Sync Scheduling
Automatic daily sync at midnight (00:00) using Laravel Scheduler.
Configuration in routes/console.php

If you want to enable the job to sync daily:
```bash
 cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

or you can test it manually:
```bash
php artisan schedule:run
```
Logs are written to storage/logs/provider-sync.log.

## Unit Tests

Feature tests verify the endpoint in key scenarios:
- Valid date range returns events (200 + data)
- No events in range returns empty array (200 + [])
- Invalid date format returns 422 with validation errors
- Starts_at after ends_at returns 422
- Missing parameters returns 422
- Provider down returns historical data from DB

Run tests with:
```bash
vendor/bin/phpunit tests/Feature/EventsEndpointTest.php
```
or 
```bash
php artisan test
```

## Performance & Scalability
- Endpoint responds in <100ms (queries local DB)
- Indexed on starts_at, ends_at, sell_mode
- For thousands of plans/zones: Redis caching + queue for sync
- High traffic (5k-10k req/s): Laravel Octane + Redis rate limiting

Contact: @nelsonhrj

