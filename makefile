all:
	echo Available targets: clean, deep-clean, setup, update

setup: clean get-composer
	echo Getting dependencies...
	php ./composer.phar install

update:
	echo Updating composer...
	php ./composer.phar self-update
	echo Updating dependencies...
	php composer.phar update

clean:
	echo Cleaning dependencies...
	rm -rf vendor

deep-clean: clean
	echo Removing composer...
	rm -rf composer.phar

get-composer: deep-clean
	echo Getting composer executable
	curl -sS https://getcomposer.org/installer | php