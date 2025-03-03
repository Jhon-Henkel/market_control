backend-bash:
	@echo "Starting bash..."
	docker compose start && docker exec -it mc_app bash

tail:
	@echo "Tailing logs..."
	tail -f -n 100 backend/storage/logs/laravel.log

rebuild-container $(container):
	@echo "Rebuilding container..."
	docker compose stop $(container) && docker compose rm -f $(container) && docker compose build $(container) && docker compose up -d $(container)

PHONY: backend-bash tail rebuild-container