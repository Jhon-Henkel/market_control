backend-bash:
	@echo "Starting bash..."
	docker compose start && docker exec -it mc_app bash

tail:
	@echo "Tailing logs..."
	tail -f -n 100 backend/storage/logs/laravel.log

PHONY: backend-bash tail