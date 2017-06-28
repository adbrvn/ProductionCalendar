PHPCOMPOSER=composer
.PHONY: all test clean _composer

all: vendor

test: vendor
	vendor/bin/phpunit tests/

vendor: _composer
	$(PHPCOMPOSER) install

_composer:
	@$(PHPCOMPOSER) -V || (echo "Could't not find composer. See https://getcomposer.org/" && exit 1)

clean:
	rm -fr vendor