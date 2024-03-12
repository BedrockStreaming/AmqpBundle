SOURCE_DIR = $(shell pwd)
BIN_DIR ?= ${SOURCE_DIR}/bin

define printSection
	@printf "\033[36m\n==================================================\033[0m\n"
	@printf "\033[36m $1 \033[0m"
	@printf "\033[36m\n==================================================\033[0m\n"
endef

.PHONY: all
all: install ci

.PHONY: ci
ci: quality test

.PHONY: install
install: clean-vendor composer-install

.PHONY: quality
quality: cs-ci phpstan

.PHONY: test
test: atoum

.PHONY: clean-vendor
clean-vendor:
	$(call printSection,CLEAN VENDOR)
	rm -rf ${SOURCE_DIR}/vendor

.PHONY: phpstan
phpstan: phpstan-cache-clear
	$(call printSection,PHPSTAN)
	${BIN_DIR}/phpstan.phar analyse --memory-limit=1G

.PHONY: phpstan-cache-clear
phpstan-cache-clear:
	${BIN_DIR}/phpstan.phar clear-result-cache

composer-install:
	$(call printSection,COMPOSER INSTALL)
	composer --no-interaction install --ansi --no-progress --prefer-dist

atoum:
	$(call printSection,TESTING)
	${BIN_DIR}/atoum --no-code-coverage --verbose

.PHONY: cs
cs: composer-install
	${BIN_DIR}/php-cs-fixer fix --dry-run --stop-on-violation --diff

.PHONY: cs-fix
cs-fix: composer-install
	${BIN_DIR}/php-cs-fixer fix

.PHONY: cs-ci
cs-ci: composer-install
	$(call printSection,PHPCS)
	${BIN_DIR}/php-cs-fixer fix --dry-run --using-cache=no --verbose
