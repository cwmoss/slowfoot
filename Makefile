all: make-docs test analyze

make-docs:
	bin/slowfoot build -d docs_src/ -f

test: 
	php vendor/bin/phpunit tests

analyze:
	vendor/bin/phpstan analyse --memory-limit 1G -l 1

min-ui:
	rollup -f es ui/app.js > ui/app-bundle.js
	terser ui/app-bundle.js -o ui/app-bundle.min.js