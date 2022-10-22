.SILENT:
MAKEFILE_ROOT ?= .
include ${MAKEFILE_ROOT}/vendor/sigwin/infra/resources/PHP/library.mk

define environment
	$(shell test -f ${BUILD_ENV}-${1} && echo -n ${BUILD_ENV}-${1} || echo ${1})
endef

# TODO: generalize and move to sigwin/infra
all/%: % ### Recursive rules
	MAKEFILE_ROOT=../../.. $(MAKE) -C src/Bridge/PhpSpreadsheet -f ../../../Makefile $<
	MAKEFILE_ROOT=../../.. $(MAKE) -C src/Bridge/Spout -f ../../../Makefile $<
	MAKEFILE_ROOT=../../.. $(MAKE) -C src/Bridge/Symfony -f ../../../Makefile $<
	MAKEFILE_ROOT=../.. $(MAKE) -C src/Xezilaires -f ../../Makefile $<

vendor/sigwin/infra/resources/PHP/library.mk:
	mv composer.json composer.json~ && rm -f composer.lock
	docker run --rm --user '$(shell id -u):$(shell id -g)' --volume '$(shell pwd):/app' --workdir /app composer:2 require sigwin/infra
	mv composer.json~ composer.json && rm -f composer.lock
