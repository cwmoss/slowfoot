APP = slowfoot
PHAR = slowfoot.phar
# MICROSFX = ~/dev/microsfx/intl
MICROSFX = ~/dev/microsfx/intl-8.5.11
BUILD = build
ARCHIVE_PREFIX = php-micro-8.5

all: make-docs test analyze

app:
	# composer install --no-dev --classmap-authoritative
	php -d phar.readonly=0 gen_phar.php

make-docs:
	bin/slowfoot build -d=docs/ -f -p=/slowfoot

test: 
	php vendor/bin/phpunit tests

analyze:
	vendor/bin/phpstan analyse --memory-limit 1G -l 1

min-ui:
	rollup -f es ui/app.js > ui/app-bundle.js
	terser ui/app-bundle.js -o ui/app-bundle.min.js

runner:
	# tail -n +4 -q vendor/vlucas/phpdotenv/src/**/*.php > dot.php
	# sed -i -e 's/declare/#declare/g' dot.php
	tail -n +2 -q src/functions/**/*.php > runner.php

clean:
	rm -f slowfoot slowfoot.phar slowfoot.phar.gz build/*

build: $(MICROSFX)/micro.sfx $(PHAR)
	cat $(MICROSFX)/micro.sfx $(PHAR) > $(APP) && chmod 0755 $(APP) && cp $(APP) /usr/local/bin/
	ls -alh /usr/local/bin/$(APP)

$(PHAR): bin/slowfoot src/*.php src/**/*.php
	composer install --no-dev --classmap-authoritative
	php -d phar.readonly=0 gen_phar.php $(PHAR) bin/slowfoot > phar.log

release: clean build-all checksums

build-all: $(PHAR)
	mkdir -p $(BUILD)
	rm -rf $(BUILD)/micro.sfx $(BUILD)/$(APP)
	cp $(PHAR) $(BUILD)/
	cd $(BUILD) && tar xfz $(MICROSFX)/$(ARCHIVE_PREFIX)-linux-aarch64.zip \
		&& cat micro.sfx $(PHAR) > $(APP) && chmod 0755 $(APP) \
		&& tar cfz $(APP)-linux-aarch64.tar.gz $(APP)
	cd $(BUILD) && tar xfz $(MICROSFX)/$(ARCHIVE_PREFIX)-linux-x86_64.zip \
		&& cat micro.sfx $(PHAR) > $(APP) && chmod 0755 $(APP) \
		&& tar cfz $(APP)-linux-x86_64.tar.gz $(APP)
	cd $(BUILD) && tar xfz $(MICROSFX)/$(ARCHIVE_PREFIX)-macos-aarch64.zip \
		&& cat micro.sfx $(PHAR) > $(APP) && chmod 0755 $(APP) \
		&& tar cfz $(APP)-macos-aarch64.tar.gz $(APP)
	#cd $(BUILD) && tar xfz $(MICROSFX)/$(ARCHIVE_PREFIX)-macos-x86_64.tar.gz \
	#	&& cat micro.sfx $(PHAR) > $(APP) && chmod 0755 $(APP) \
	#	&& tar cfz $(APP)-macos-x86_64.tar.gz $(APP)
	#cd $(BUILD) && tar xfz $(MICROSFX)/$(ARCHIVE_PREFIX)-win.zip \
	#	&& cat micro.sfx $(PHAR) > $(APP).exe && chmod 0755 $(APP).exe \
	#	&& zip $(APP)-win-x86_64.zip $(APP).exe

checksums:
	echo '  // Generating checksums...'
	# $(APP)-linux-aarch64.tar.gz $(APP)-macos-x86_64.tar.gz $(APP)-win-x86_64.zip
	cd build && sha256sum \
	$(APP)-linux-x86_64.tar.gz $(APP)-linux-aarch64.tar.gz \
	$(APP)-macos-aarch64.tar.gz \
	$(PHAR) > checksums.txt

$(MICROSFX)/micro.sfx: $(MICROSFX)/$(ARCHIVE_PREFIX)-macos-aarch64.zip
	tar xfzm $< && mv micro.sfx $(MICROSFX)/
