ifndef BUILD_ENV
BUILD_ENV=8.0
endif

ifndef MAKEFILE_ROOT
MAKEFILE_ROOT=.
endif

PHPSTAN_OUTPUT=
PSALM_OUTPUT=
define start
endef
define end
endef
ifdef GITHUB_ACTIONS
define start
echo ::group::$(1) in ${CURDIR}
endef
define end
echo ::endgroup::
endef
PHPSTAN_OUTPUT=--error-format=github
PSALM_OUTPUT=--output-format=github
endif

ifndef TTY
TTY:=$(shell [ -t 0 ] && echo --tty)
endif

ifndef DOCQA_DOCKER_COMMAND
DOCQA_DOCKER_IMAGE=dkarlovi/docqa:latest
DOCQA_DOCKER_COMMAND=docker run --init --interactive ${TTY} --rm --env HOME=/tmp --user "$(shell id -u):$(shell id -g)"  --volume "$(shell pwd)/${MAKEFILE_ROOT}/docs:/config" --volume "$(shell pwd)/var/tmp/docqa:/.cache" --volume "$(shell pwd):/project" --workdir /project ${DOCQA_DOCKER_IMAGE}
endif

ifndef PHPQA_DOCKER_COMMAND
PHPQA_DOCKER_IMAGE=jakzal/phpqa:1.64.0-php${BUILD_ENV}-alpine
PHPQA_DOCKER_COMMAND=docker run --init --interactive ${TTY} --rm --env "COMPOSER_CACHE_DIR=/composer/cache" --user "$(shell id -u):$(shell id -g)" --volume "$(shell pwd)/var/tmp/phpqa:/tmp" --volume "$(shell pwd):/project" --volume "${HOME}/.composer:/composer" --workdir /project ${PHPQA_DOCKER_IMAGE}
endif

.SILENT:

dist: composer-normalize-all cs check-all test-all docs-all
check: composer-normalize-check phpstan psalm
test: infection
docs: markdownlint vale

define process
	(cd src/Bridge/PhpSpreadsheet && MAKEFILE_ROOT=../../.. make -f ../../../Makefile $(1))
	(cd src/Bridge/Spout && MAKEFILE_ROOT=../../.. make -f ../../../Makefile $(1))
	(cd src/Bridge/Symfony && MAKEFILE_ROOT=../../.. make -f ../../../Makefile $(1))
	(cd src/Xezilaires && MAKEFILE_ROOT=../.. make -f ../../Makefile $(1))
endef

define environment
	$(shell test -f ${BUILD_ENV}-${1} && echo -n ${BUILD_ENV}-${1} || echo ${1})
endef

check-all: cs-check check
	$(call process,check)
test-all: test
	$(call process,test)
composer-install-all: composer-install
	$(call process,composer-install)
composer-install-lowest-all: composer-install-lowest
	$(call process,composer-install-lowest)
composer-normalize-all: composer-normalize
	$(call process,composer-normalize)
composer-normalize-check-all: composer-normalize-check
	$(call process,composer-normalize-check)
phpunit-all: phpunit
	$(call process,phpunit)
phpunit-coverage-all: phpunit-coverage
	$(call process,phpunit-coverage)
docs-all: docs
	$(call process,docs)
ensure-all: ensure
	$(call process,ensure)
clean-all: clean
	$(call process,clean)

composer-validate: ensure composer-normalize-check
	sh -c "${PHPQA_DOCKER_COMMAND} composer validate --no-check-lock"
composer-install: ensure
	$(call start,Composer install)
	sh -c "${PHPQA_DOCKER_COMMAND} composer upgrade"
	$(call end)
composer-install-lowest: ensure
	$(call start,Composer install)
	sh -c "${PHPQA_DOCKER_COMMAND} composer upgrade --with-all-dependencies --prefer-lowest"
	$(call end)
composer-normalize: ensure
	sh -c "${PHPQA_DOCKER_COMMAND} composer normalize --no-check-lock"
composer-normalize-check: ensure
	$(call start,Composer normalize)
	sh -c "${PHPQA_DOCKER_COMMAND} composer normalize --no-check-lock --dry-run"
	$(call end)

cs: ensure
	sh -c "${PHPQA_DOCKER_COMMAND} php-cs-fixer fix --diff -vvv"
cs-check: ensure
	$(call start,PHP CS Fixer)
	sh -c "${PHPQA_DOCKER_COMMAND} php-cs-fixer fix --dry-run --diff -vvv"
	$(call end)

phpstan: ensure
	$(call start,PHPStan)
	sh -c "${PHPQA_DOCKER_COMMAND} phpstan analyse ${PHPSTAN_OUTPUT} --configuration $(call environment,phpstan.neon.dist)"
	$(call end)

psalm: ensure
	$(call start,Psalm)
	sh -c "${PHPQA_DOCKER_COMMAND} psalm --php-version=${BUILD_ENV} ${PSALM_OUTPUT} --config $(call environment,psalm.xml.dist)"
	$(call end)

phpunit:
	$(call start,PHPUnit)
	sh -c "${PHPQA_DOCKER_COMMAND} vendor/bin/phpunit --verbose"
	$(call end)
phpunit-coverage: ensure
	$(call start,PHPUnit)
	sh -c "${PHPQA_DOCKER_COMMAND} php -d pcov.enabled=1 vendor/bin/phpunit --verbose --coverage-text --log-junit=var/junit.xml --coverage-xml var/coverage-xml/"
	$(call end)

infection: phpunit-coverage
	$(call start,Infection)
	sh -c "${PHPQA_DOCKER_COMMAND} infection run --verbose --show-mutations --no-interaction --only-covered --coverage var/ --threads 4"
	$(call end)

markdownlint: ensure
	$(call start,Markdownlint)
	sh -c "${DOCQA_DOCKER_COMMAND} markdownlint README.md"
	$(call end)
vale: ensure
	$(call start,Vale)
	sh -c "${DOCQA_DOCKER_COMMAND} vale --config /config/.vale.ini.dist README.md"
	$(call end)

ensure: clean
	mkdir -p ${HOME}/.composer var/tmp/docqa var/tmp/phpqa
fetch:
	docker pull "${DOCQA_DOCKER_IMAGE}"
	docker pull "${PHPQA_DOCKER_IMAGE}"
clean:
	rm -rf var/ .phpunit.result.cache composer.lock
