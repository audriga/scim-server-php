# Default port to start server on
PORT := 8888

.PHONY: clean
clean:
	rm -rf ./vendor

.PHONY: install
install:
	composer install --prefer-dist
	php db/database.php

.PHONY: start-server
start-server:
	composer install --prefer-dist
	php db/database.php
	php -S localhost:$(PORT) -t public/ public/index.php

# linting based on https://github.com/dbfx/github-phplint
.PHONY: lint
lint:
	# Make sure we don't lint test classes
	composer install --prefer-dist --no-dev

	# Lint for installed PHP version
	sh -c "! (find . -type f -name \"*.php\" -not -path \"./build/*\" -not -path \"./vendor/*\" $1 -exec php -l -n {} \; | grep -v \"No syntax errors detected\")"

	# Make devtools available again
	composer install --prefer-dist

	# Lint with CodeSniffer
	vendor/bin/phpcs --standard=phpcs.xml src/

.PHONY: api_test
api_test:
# If we pass the PFA_API_TEST variable with value 1, we can run the PFA API tests
ifeq ($(PFA_API_TEST),1)
	newman run test/postman/scim-opf-pfa.postman_collection.json -e test/postman/scim-env.postman_environment.json
else
	newman run test/postman/scim-opf.postman_collection.json -e test/postman/scim-env.postman_environment.json
endif

.PHONY: unit_test
unit_test:
	composer install --prefer-dist
	vendor/bin/phpunit -c test/phpunit.xml --testdox

.PHONY: fulltest
fulltest: lint api_test unit_test
