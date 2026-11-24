# Docker-based development helpers: everything runs on PHP 8.1, the minimum supported version.
export UID := $(shell id -u)
export GID := $(shell id -g)

RUN := docker compose run --rm app

.PHONY: help build install update composer test lint shell

help: ## List available targets
	@grep -E '^[a-zA-Z_-]+:.*?## ' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[36m%-14s\033[0m %s\n", $$1, $$2}'

build: ## Build the PHP 8.1 image
	docker compose build app

install: ## composer install
	$(RUN) composer install

update: ## composer update
	$(RUN) composer update

composer: ## Run any composer command: make composer ARGS="require foo/bar"
	$(RUN) composer $(ARGS)

test: ## Pest (calls the live API in .env): make test ARGS="--filter=Foo"
	$(RUN) composer test -- $(ARGS)

lint: ## Fix code style with Pint
	$(RUN) composer lint

shell: ## Shell inside the PHP container
	$(RUN) bash
