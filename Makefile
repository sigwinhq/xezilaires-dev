.SILENT:
include vendor/sigwin/infra/resources/PHP/library.mk

#dist: composer-normalize-all cs check-all test-all docs-all
#check: composer-normalize-check phpstan psalm
#test: infection
#docs: markdownlint vale

define process
	(cd src/Bridge/PhpSpreadsheet && MAKEFILE_ROOT=../../.. make -f ../../../Makefile $(1))
	(cd src/Bridge/Spout && MAKEFILE_ROOT=../../.. make -f ../../../Makefile $(1))
	(cd src/Bridge/Symfony && MAKEFILE_ROOT=../../.. make -f ../../../Makefile $(1))
	(cd src/Xezilaires && MAKEFILE_ROOT=../.. make -f ../../Makefile $(1))
endef

define environment
	$(shell test -f ${BUILD_ENV}-${1} && echo -n ${BUILD_ENV}-${1} || echo ${1})
endef
