.PHONY: docs

docs:
	@echo 'Building API documentation...'
	node_modules/aglio/bin/aglio.js -i docs/index.apib -o public/docs/index.html
	@echo 'done!'
