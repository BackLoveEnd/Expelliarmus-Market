build:
	docker compose up -d --build

start:
	docker compose up -d

front-dev:
	docker exec -it npm npm run dev

full-rebuild:
	docker compose down -v
	docker compose build
	docker exec php composer install
	docker exec npm npm run install
	docker exec php php artisan migrate:fresh --seed --storage-clean
	docker exec php php artisan storage:unlink
	docker exec php php artisan storage:link

migrate-seed:
	docker exec php php artisan migrate:fresh --seed --storage-clean