# Makefile for Fever Provider Integration (Laravel 12)

# Variables
PHP             = php
ARTISAN         = $(PHP) artisan
COMPOSER        = composer
NPM             = npm
DOCKER_COMPOSE  = docker compose

# Colors for output
GREEN  := $(shell tput -Txterm setaf 2)
YELLOW := $(shell tput -Txterm setaf 3)
WHITE  := $(shell tput -Txterm setaf 7)
CYAN   := $(shell tput -Txterm setaf 6)
RESET  := $(shell tput -Txterm sgr0)

# Default help target
.PHONY: help
help: ## Show this help message
	@echo ''
	@echo 'Usage:'
	@echo '  ${CYAN}make${RESET} ${GREEN}<target>${RESET}'
	@echo ''
	@echo 'Available targets:'
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "  ${CYAN}%-30s${RESET} %s\n", $$1, $$2}'

.PHONY: install
install: ## Install dependencies (Composer + NPM if needed)
	$(COMPOSER) install --prefer-dist --no-progress --no-interaction
	$(ARTISAN) key:generate --force --ansi
	@echo "${GREEN}Installation completed${RESET}"

.PHONY: setup
setup: install migrate seed ## Full setup: dependencies + DB + seeders (if any)

.PHONY: migrate
migrate: ## Run migrations (create tables)
	$(ARTISAN) migrate --force --ansi

.PHONY: migrate-fresh
migrate-fresh: ## Drop and recreate database + run migrations
	$(ARTISAN) migrate:fresh --seed --force --ansi

.PHONY: sync
sync: ## Sync events from the external provider
	$(ARTISAN) provider:sync

.PHONY: serve
serve: ## Start the development server (http://localhost:8000)
	$(ARTISAN) serve --port=8000

.PHONY: test
test: ## Run all tests
	$(ARTISAN) test --order-by=random

.PHONY: tinker
tinker: ## Open Tinker (interactive REPL)
	$(ARTISAN) tinker

.PHONY: clear
clear: ## Clear all caches
	$(ARTISAN) optimize:clear
	$(ARTISAN) cache:clear
	$(ARTISAN) config:clear
	$(ARTISAN) route:clear
	$(ARTISAN) view:clear

.PHONY: logs
logs: ## Tail sync logs in real-time
	tail -f storage/logs/provider-sync.log

.PHONY: schedule-run
schedule-run: ## Run the scheduler manually (useful for testing)
	$(ARTISAN) schedule:run

# Optional: Docker targets (recommended for the challenge if using containers)
.PHONY: docker-up
docker-up: ## Start containers with Docker Compose (if docker-compose.yml exists)
	$(DOCKER_COMPOSE) up -d

.PHONY: docker-down
docker-down: ## Stop and remove containers
	$(DOCKER_COMPOSE) down

# Useful aliases
.PHONY: run
run: clear serve ## Clear caches and start the server

.PHONY: fresh
fresh: migrate-fresh sync serve ## Clean start: fresh DB + sync + serve
