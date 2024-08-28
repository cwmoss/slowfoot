all: make-docs run-tests analyze

make-docs:
	bin/slowfoot build -d docs_src/ -f

run-tests:
	php vendor/bin/phpunit run tests

analyze:
	vendor/bin/phpstan analyse --memory-limit 1G -l 1