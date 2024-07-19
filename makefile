# Comando para empaquetar la carpeta en un archivo ZIP
zip:
	zip -r -v -FS ai-entries.zip ai-entries

# Run locally
start: 
	npx wp-env start --xdebug=coverage		
	npx wp-env run cli --env-cwd=wp-content/plugins/ai-entries rm -rf vendor
	npx wp-env run cli --env-cwd=wp-content/plugins/ai-entries composer require phpunit/phpunit  --dev --with-all-dependencies	
	npx wp-env run cli --env-cwd=wp-content/plugins/ai-entries composer require yoast/phpunit-polyfills  --dev --with-all-dependencies
	npx wp-env run cli --env-cwd=wp-content/plugins/ai-entries composer require 10up/wp_mock --dev --with-all-dependencies
# Tests
test:				
	npx wp-env run tests-cli --env-cwd=wp-content/plugins/ai-entries ./vendor/bin/phpunit ./tests/classes --bootstrap ./tests/bootstrap.php --testdox --colors
	
# Clean all environments
clear:
	npx wp-env clean all

# Destroy environment
destroy:
	npx wp-env destroy

# Debug environment
debug:
	npx wp-env logs --debug --watch