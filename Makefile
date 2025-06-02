include .env
-include .env.local
export

.PHONY: help

ENV ?= dev
COMPOSE_OPTIONS =

ifeq ($(ENV), dev)
COMPOSE_OPTIONS = -f compose.override.yaml
endif

COMPOSE = docker-compose -f compose.yaml $(COMPOSE_OPTIONS)
EXEC_APP = $(COMPOSE) exec app

help: ## Displays help for a command
	@printf "\033[33mUsage:\033[0m\n  make [options] [target] ...\n\n\033[33mAvailable targets:%-13s\033[0m\n"
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' 'Makefile' | awk 'BEGIN {FS = ":.*?## "}; {printf "%-2s\033[32m%-20s\033[0m %s\n", "", $$1, $$2}'

build: ## Build containers
	$(COMPOSE) build

pull:
	$(COMPOSE) pull

up: ## Start environment
	$(COMPOSE) up --detach --remove-orphans
	$(COMPOSE) ps

down: ## Stop environment
	$(COMPOSE) down

check-style: ## Check the application code style
	$(EXEC_APP) php -d memory_limit=-1 vendor/bin/phpcs -v
	$(EXEC_APP) php -d memory_limit=-1 vendor/bin/phpstan analyse -l1 -v

tests-run: ## Run application test cases
	$(EXEC_APP) php -d memory_limit=-1 bin/phpunit tests
