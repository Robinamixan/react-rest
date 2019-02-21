#!/bin/bash

RED=\033[0;31m
GREEN=\033[0;32m
NOCOLOUR=\033[0m
PACK=null

.PHONY: all
all: 
	make usage

.PHONY: usage
usage: 
	@printf "$(GREEN)usage:\n" ;
	@printf "$(GREEN)init: $(NOCOLOUR) initialise the app\n" ;
	@printf "$(GREEN)start: $(NOCOLOUR) starts the docker dev env from the $(GREEN)docker-compose.yml \n" ;
	@printf "$(GREEN)stop: $(NOCOLOUR) stops the docker containers in the $(GREEN)docker-compose.yml \n" ;
	@printf "$(GREEN)rebuild-and-start: $(NOCOLOUR) rebuilds and starts the docker containers in the $(GREEN)docker-compose.yml \n" ;
	@printf "$(GREEN)sy-require PACK=package-name $(NOCOLOUR): run composer require inside the php container\n" ;
	@printf "$(GREEN)sy-remove PACK=package-name $(NOCOLOUR): run composer remove inside the php container\n" ;
	@printf "$(GREEN)sy-run C=cache:clear $(NOCOLOUR):run the symfony console inside the php container\n" ;
	@printf "$(GREEN)run C=\"some command\" $(NOCOLOUR):run the command inside the php container\n" ;

.PHONY: init
init:
	@if [ -e ".env" ]; then \
			printf "$(GREEN).env file exists$(NOCOLOUR)\n" ; \
	else \
	        cp .env.dist .env; \
    		printf "$(GREEN).env file created from .env.dist$(NOCOLOUR)\n" ; \
	fi;
	@docker-compose -f docker-compose.yml build
	@docker-compose -f docker-compose.yml start
	@docker-compose -f docker-compose.yml run --rm php composer install;
	@docker-compose -f docker-compose.yml run --rm php  /bin/bash -c "bin/console do:da:cr --if-not-exists && bin/console do:mi:mi -n";
	@printf "$(GREEN)your dependencies were installed and the app is ready to be run\n" ; \

.PHONY: start
start:
	docker-compose -f docker-compose.yml up -d

.PHONY: stop
stop:
	docker-compose -f docker-compose.yml stop

.PHONY: rebuild-and-start
rebuild-and-start:
	docker-compose up -f docker-compose.yml --build -d

.PHONY: require
sy-require:
	@if [ "$(PACK)" = "null" ]; then \
		printf "$(RED)Package needs to be passed as argument: PACK \n" ; \
	else \
		printf "Package: $(RED) <$(PACK)> $(NOCOLOUR) is being required \n" ; \
		docker-compose -f docker-compose.yml run --rm php composer require $(PACK); \
	fi

.PHONY: remove
sy-remove:
	@if [ "$(PACK)" = "null" ]; then \
		printf "$(RED)Package needs to be passed as argument: PACK \n" ; \
	else \
		printf "Package: $(RED) <$(PACK)> $(NOCOLOUR)is being removed \n" ; \
		docker-compose -f docker-compose.yml run --rm php composer remove $(PACK); \
	fi

.PHONY: sy-run
sy-run:
	@printf "Running Symfony command: <$(RED)$(C)$(NOCOLOUR)> \n" ; \
	docker-compose run --rm php bin/console $(C); \

.PHONY: run
run:
	@if [ "$(C)" = "" ]; then \
		printf "$(RED)To run a command in the container, you needs to pass as argument: C \n" ; \
	else \
		printf "Running the command: <$(RED)$(C)$(NOCOLOUR)> \n" ; \
		docker-compose -f docker-compose.yml run --rm php $(C); \
	fi

.PHONY: test
test:
	docker-compose -f docker-compose.yml run -e APP_ENV=test --rm php bin/phpunit

.PHONY: cs-fixer-run
cs-fixer-run:
	docker-compose -f docker-compose.yml run --rm php vendor/bin/php-cs-fixer fix -v --dry-run --using-cache=no

.PHONY: cs-fixer-run-fix
cs-fixer-run-fix:
	docker-compose -f docker-compose.yml run --rm php vendor/bin/php-cs-fixer fix -v --using-cache=no
