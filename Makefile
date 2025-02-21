build:
	docker compose up -d --build

start:
	docker compose up -d

front-dev:
	docker exec -it npm npm run dev

## CHANGE ALL :KEY IN ALL LOOPS TO OBJECT ID, WHERE THAT KEY IS JUST ARRAY INDEX

//TODO: product page admin
//TODO: delete/deleteMany test