.DEFAULT_GOAL := help

first-start-app: ## First application init
	docker compose up -d --build
	docker exec php composer install
	docker exec npm npm install
	docker exec php php artisan migrate:fresh --seed --storage-clean
	docker exec php php artisan storage:link
	docker exec php php artisan key:generate

build: ## Build and run application
	docker compose up -d --build

start: ## Run application
	docker compose up -d

stop-remove: ## Stop and clear docker
	docker compose down -v

front-dev: ## Frontend Dev Server
	docker exec -it npm npm run dev

front-build: ## Build Frontend
	docker exec -it npm npm run build

full-rebuild: ## Full re-install application
	docker compose down -v
	docker compose up -d --build
	docker exec php composer install
	docker exec npm npm install
	docker exec php php artisan migrate:fresh --seed --storage-clean
	docker exec php php artisan storage:unlink
	docker exec php php artisan storage:link

migrate-seed: ## Run migration and seeders
	docker exec php php artisan migrate:fresh --seed --storage-clean

super-manager: ## Create or retrieve test super manager
	docker exec php php artisan management:create-super-manager

frontend-dependencies-install: ## Install frontend dependencies
	docker exec npm npm install

backend-dependencies-install: ## Install backend dependencies
	docker exec php composer install

backend-cache-clear: ## Clean backend application cache
	docker exec php php artisan cache:clear

backend-tests: ## Run backend tests.
	docker exec php php artisan test

.PHONY: help
help:
	@echo "Available commands:"
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'