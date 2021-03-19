ifndef BUILD_ENV
BUILD_ENV=8.0
endif

ifndef DOCQA_DOCKER_COMMAND
DOCQA_DOCKER_IMAGE=dkarlovi/docqa:latest
DOCQA_DOCKER_COMMAND=docker run --init --interactive --rm --user "$(shell id -u):$(shell id -g)"  --volume "$(shell pwd)/var/tmp/docqa:/.cache" --volume "$(shell pwd):/project" --workdir /project ${DOCQA_DOCKER_IMAGE}
endif

ifndef PHPQA_DOCKER_COMMAND
PHPQA_DOCKER_IMAGE=jakzal/phpqa:1.53-php${BUILD_ENV}-alpine
PHPQA_DOCKER_COMMAND=docker run --init --interactive --rm --env "COMPOSER_CACHE_DIR=/composer/cache" --user "$(shell id -u):$(shell id -g)" --volume "$(shell pwd)/var/tmp/phpqa:/tmp" --volume "$(shell pwd):/project" --volume "${HOME}/.composer:/composer" --workdir /project ${PHPQA_DOCKER_IMAGE}
endif

ifndef MAKEFILE_ROOT
MAKEFILE_ROOT=.
endif

dist: composer-normalize-all cs check-all test-all doc-all
check: composer-normalize-check phpstan psalm
test: infection
doc: markdownlint textlint proselint vale

define process
	(cd src/Bridge/PhpSpreadsheet && MAKEFILE_ROOT=../../.. make -f ../../../Makefile $(1))
	(cd src/Bridge/Spout && MAKEFILE_ROOT=../../.. make -f ../../../Makefile $(1))
	(cd src/Bridge/Symfony && MAKEFILE_ROOT=../../.. make -f ../../../Makefile $(1))
	(cd src/Xezilaires && MAKEFILE_ROOT=../../.. make -f ../../Makefile $(1))
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
doc-all: doc
	$(call process,doc)
ensure-all: ensure
	$(call process,ensure)
clean-all: clean
	$(call process,clean)

composer-validate: ensure composer-normalize-check
	sh -c "${PHPQA_DOCKER_COMMAND} composer validate --no-check-lock"
composer-install: ensure
	sh -c "${PHPQA_DOCKER_COMMAND} composer upgrade"
composer-install-lowest: ensure
	sh -c "${PHPQA_DOCKER_COMMAND} composer upgrade --with-all-dependencies --prefer-lowest"
composer-normalize: ensure
	sh -c "${PHPQA_DOCKER_COMMAND} composer normalize --no-check-lock"
composer-normalize-check: ensure
	sh -c "${PHPQA_DOCKER_COMMAND} composer normalize --no-check-lock --dry-run"

cs: ensure
	sh -c "${PHPQA_DOCKER_COMMAND} php-cs-fixer fix --diff -vvv"
cs-check: ensure
	sh -c "${PHPQA_DOCKER_COMMAND} php-cs-fixer fix --dry-run --diff -vvv"

phpstan: ensure
	sh -c "${PHPQA_DOCKER_COMMAND} phpstan analyse"

psalm: ensure
	sh -c "${PHPQA_DOCKER_COMMAND} psalm"

phpunit:
	sh -c "${PHPQA_DOCKER_COMMAND} vendor/bin/phpunit --verbose"
phpunit-coverage: ensure
	sh -c "${PHPQA_DOCKER_COMMAND} php -d pcov.enabled=1 vendor/bin/phpunit --verbose --coverage-text --log-junit=var/junit.xml --coverage-xml var/coverage-xml/"

infection: phpunit-coverage
	sh -c "${PHPQA_DOCKER_COMMAND} infection run --verbose --show-mutations --no-interaction --only-covered --coverage var/ --min-msi=100 --min-covered-msi=100 --threads 4"

markdownlint: ensure
	sh -c "${DOCQA_DOCKER_COMMAND} markdownlint README.md"
proselint: ensure
	sh -c "${DOCQA_DOCKER_COMMAND} proselint README.md"
textlint: ensure
	sh -c "${DOCQA_DOCKER_COMMAND} textlint -c ${MAKEFILE_ROOT}/docs/.textlintrc.dist README.md"
vale: ensure
	sh -c "${DOCQA_DOCKER_COMMAND} vale --config ${MAKEFILE_ROOT}/docs/.vale.ini.dist README.md"

ensure: clean
	mkdir -p ${HOME}/.composer var/tmp/docqa var/tmp/phpqa
fetch:
	docker pull "${DOCQA_DOCKER_IMAGE}"
	docker pull "${PHPQA_DOCKER_IMAGE}"
clean:
	rm -rf var/ .phpunit.result.cache composer.lock
