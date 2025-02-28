.PHONY: help
help: ## Show this help menu
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

#######################
# Docker Commands
#######################

.PHONY: up
up: ## Start the Docker containers
	docker compose up -d

.PHONY: down
down: ## Stop the Docker containers
	docker compose down

.PHONY: build
build: ## Build the Docker containers
	docker compose build

.PHONY: rebuild
rebuild: ## Rebuild the Docker containers from scratch
	docker compose build --no-cache

.PHONY: restart
restart: down up ## Restart the Docker containers

.PHONY: logs
logs: ## View Docker container logs
	docker compose logs -f

#######################
# Development Commands
#######################

.PHONY: install
install: ## Install PHP and Node.js dependencies
	docker compose exec app composer install
	docker compose exec app npm install

.PHONY: fresh
fresh: ## Fresh install all dependencies and rebuild assets
	docker compose exec app composer install
	docker compose exec app npm install
	docker compose exec app npm run build
	docker compose exec app php artisan migrate:fresh --seed

.PHONY: migrate
migrate: ## Run database migrations
	docker compose exec app php artisan migrate

.PHONY: seed
seed: ## Run database seeders
	docker compose exec app php artisan db:seed

.PHONY: rollback
rollback: ## Rollback database migrations
	docker compose exec app php artisan migrate:rollback

.PHONY: cache
cache: ## Clear all Laravel caches
	docker compose exec app php artisan cache:clear
	docker compose exec app php artisan config:clear
	docker compose exec app php artisan route:clear
	docker compose exec app php artisan view:clear

.PHONY: dev
dev: ## Start development servers (Vite, Queue, Pail)
	@make up
	docker compose exec app npm run dev & \
	docker compose exec app php artisan queue:work & \
	docker compose exec app php artisan pail

.PHONY: stop
stop: ## Stop development servers
	docker compose down

.PHONY: test
test: ## Run PHPUnit tests
	docker compose exec app php artisan test

.PHONY: lint
lint: ## Run Laravel Pint
	docker compose exec app ./vendor/bin/pint

.PHONY: shell
shell: ## Access the container shell
	docker compose exec app sh

.PHONY: tinker
tinker: ## Access Laravel Tinker
	docker compose exec app php artisan tinker

#######################
# Setup Commands
#######################

.PHONY: setup
setup: build up install env ## Initial project setup
	@echo "Waiting for MySQL to be ready..."
	@while ! docker compose exec mysql mysqladmin ping -h localhost -u root -psecret --silent; do \
		sleep 1; \
	done
	docker compose exec app php artisan key:generate
	docker compose exec app php artisan migrate
	docker compose exec app npm run build

.PHONY: env
env: ## Create .env file if it doesn't exist
	@if [ ! -f .env ]; then \
		cp .env.example .env; \
	fi

#######################
# Database Commands
#######################

.PHONY: db-fresh
db-fresh: ## Drop all tables and re-run migrations
	docker compose exec app php artisan migrate:fresh --seed

.PHONY: db-reset
db-reset: ## Reset and seed database
	docker compose exec app php artisan migrate:reset
	docker compose exec app php artisan migrate
	docker compose exec app php artisan db:seed

.PHONY: db-shell
db-shell: ## Access MySQL shell
	docker compose exec mysql mysql -u laravel -psecret laravel

.PHONY: db-backup
db-backup: ## Backup the database
	@mkdir -p ./storage/backups
	docker compose exec mysql mysqldump -u laravel -psecret laravel > ./storage/backups/backup-$(shell date +%Y%m%d%H%M%S).sql

.PHONY: db-restore
db-restore: ## Restore the database from a backup file (use with DB_BACKUP=filename.sql)
	@if [ -z "$(DB_BACKUP)" ]; then \
		echo "Please specify a backup file with DB_BACKUP=filename.sql"; \
		exit 1; \
	fi
	docker compose exec -T mysql mysql -u laravel -psecret laravel < $(DB_BACKUP)

.PHONY: wait-for-db
wait-for-db: ## Wait for MySQL to be ready
	@echo "Waiting for MySQL to be ready..."
	@while ! docker compose exec mysql mysqladmin ping -h localhost -u root -psecret --silent; do \
		sleep 1; \
	done
