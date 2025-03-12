build:
	docker compose up -d --build

start:
	docker compose up -d

front-dev:
	docker exec -it npm npm run dev