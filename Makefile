ifndef BUILD_ENV
BUILD_ENV=php7.3
endif

QA_DOCKER_IMAGE=jakzal/phpqa:1.17-${BUILD_ENV}-alpine
QA_DOCKER_COMMAND=docker run --init --interactive --tty --rm --env "COMPOSER_HOME=/composer" --user "$(shell id -u):$(shell id -g)" --volume /tmp/tmp-phpqa-$(shell id -u):/tmp --volume "$(shell pwd):/project" --volume "${HOME}/.composer:/composer" --workdir /project ${QA_DOCKER_IMAGE}

dist: composer-validate cs phpstan psalm test
ci: check test
check: composer-validate cs-check phpstan psalm
test: infection

clean:
	rm -rf var/

composer-validate: ensure
	sh -c "${QA_DOCKER_COMMAND} composer validate"

composer-install: fetch ensure clean
	sh -c "${QA_DOCKER_COMMAND} composer upgrade"

composer-install-lowest: fetch ensure clean
	sh -c "${QA_DOCKER_COMMAND} composer upgrade --prefer-lowest"

cs: ensure
	sh -c "${QA_DOCKER_COMMAND} php-cs-fixer fix --using-cache=false --diff -vvv"

cs-check: ensure
	sh -c "${QA_DOCKER_COMMAND} php-cs-fixer fix --using-cache=false --dry-run --diff -vvv"

phpstan: ensure
	sh -c "${QA_DOCKER_COMMAND} phpstan analyse"

psalm: ensure
	sh -c "${QA_DOCKER_COMMAND} psalm --show-info=false --threads max"

phpunit:
	sh -c "${QA_DOCKER_COMMAND} phpunit-7 --verbose"

phpunit-coverage: ensure
	sh -c "${QA_DOCKER_COMMAND} phpdbg -dauto_globals_jit=0 -qrr /tools/phpunit-7 --verbose --coverage-text --log-junit=var/phpunit.junit.xml --coverage-xml var/coverage-xml/"

infection: phpunit-coverage
	sh -c "${QA_DOCKER_COMMAND} phpdbg -dauto_globals_jit=0 -qrr /tools/infection run --verbose --show-mutations --no-interaction --only-covered --coverage var/ --min-msi=100 --min-covered-msi=100 --threads 4"

ensure:
	mkdir -p ${HOME}/.composer /tmp/tmp-phpqa-$(shell id -u)

fetch:
	docker pull "${QA_DOCKER_IMAGE}"
