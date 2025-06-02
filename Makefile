DOCKER_COMPOSE := docker compose --env-file .env -f docker-compose.yaml

.PHONY: init
init:
	$(DOCKER_COMPOSE) run --name init vite npm install --prefix vite && \
	docker container rm init

.PHONY: php
php:
	$(DOCKER_COMPOSE) up

.PHONY: vite
vite:
	$(DOCKER_COMPOSE) --profile vite up

.PHONY: remove
remove:
	$(DOCKER_COMPOSE) down --volumes --remove-orphans --rmi all
