# Executables (on local machine)
DOCKER_COMP = docker compose

# Docker containers
PHP_CONT = $(DOCKER_COMP) exec php

# Executables (inside containers)
PHP      = $(PHP_CONT) php
COMPOSER = $(PHP_CONT) composer
SYMFONY  = $(PHP) bin/console

# Colors
COLOR_RESET = \033[0m
COLOR_TARGET = \033[32m
COLOR_TITLE = \033[33m
TEXT_BOLD = \033[1m

.PHONY: help
.SILENT: help
help:
	printf "\n${COLOR_TITLE}Usage:${COLOR_RESET}\n"
	printf "  ${COLOR_TARGET}make${COLOR_RESET} [target]\n"
	printf "\n"
	awk '/^[\w\.@%-]+:/i { \
		helpMessage = match(lastLine, /^### (.*)/); \
		if (helpMessage) { \
			helpCommand = substr($$1, 0, index($$1, ":") - 1); \
			helpMessage = substr(lastLine, RSTART + 3, RLENGTH); \
			printf "  ${COLOR_TARGET}%-30s${COLOR_RESET} %s\n", helpCommand, helpMessage; \
		} \
	} \
	/^##@.+/ { \
		printf "\n${TEXT_BOLD}${COLOR_TITLE}%s${COLOR_RESET}\n", substr($$0, 5); \
	} \
	{ lastLine = $$0 }' $(MAKEFILE_LIST)

##@ Init

### Install the dependencies
install:
	$(PHP_CONT) composer install

##@ Docker

### Build the Docker images
docker-build:
	$(DOCKER_COMP) build --no-cache

### Start the docker hub in detached mode (no logs)
docker-start:
	$(DOCKER_COMP) up --pull always -d --wait

### Stop the docker hub
docker-stop:
	$(DOCKER_COMP) down --remove-orphans

### Log into the PHP container
docker-sh:
	$(PHP_CONT) bash

##@ Frontend

### Start the frontend build watch
front-watch:
	$(SYMFONY) tailwind:build --watch

### Build the frontend assets
front-build:
	$(SYMFONY) tailwind:build --minify
	$(SYMFONY) asset-map:compile

##@ Code Quality

### Run test suite
qa-test: public/assets
	$(PHP) bin/phpunit

### Run PHPStan
qa-phpstan:
	$(PHP) vendor/bin/phpstan

### Run PHP-CS-Fixer (dry-run)
qa-cs:
	$(PHP) vendor/bin/php-cs-fixer fix --dry-run --diff

### Run PHP-CS-Fixer (fix)
qa-cs-fix:
	$(PHP) vendor/bin/php-cs-fixer fix

## Hidden targets

public/assets:
	make front-build
