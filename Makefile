.SILENT:
MONOREPO_ROOT ?= .
MONOREPO_DIRS=src/Bridge/PhpSpreadsheet/ src/Bridge/Spout/ src/Bridge/Symfony/ src/Xezilaires/

include ${MONOREPO_ROOT}/vendor/sigwin/infra/resources/PHP/library_monorepo.mk

vendor/sigwin/infra/resources/PHP/library_monorepo.mk:
	mv composer.json composer.json~ && rm -f composer.lock
	docker run --rm --user '$(shell id -u):$(shell id -g)' --volume '$(shell pwd):/app' --workdir /app composer:2 require sigwin/infra
	mv composer.json~ composer.json && rm -f composer.lock
