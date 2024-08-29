all: make-docs test analyze

make-docs:
	bin/slowfoot build -d docs_src/ -f

test: 
	php vendor/bin/phpunit tests

analyze:
	vendor/bin/phpstan analyse --memory-limit 1G -l 1