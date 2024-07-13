# Comando para empaquetar la carpeta en un archivo ZIP
zip:
	zip -r -v -FS ai-entries.zip ai-entries

# Run locally
test: 
	npx wp-env start

# Clean all environments
clear:
	npx wp-env clean all

# Destroy environment
destroy:
	npx wp-env destroy

# Debug environment
debug:
	npx wp-env logs --debug --watch
