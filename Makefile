backend-bash:
	@echo "Starting bash..."
	docker compose start && docker exec -it mc_app bash

PHONY: backend-bash