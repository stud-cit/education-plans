tinker:
	./vendor/bin/sail artisan tinker
docker-up:
	./vendor/bin/sail up -d
docker-down:
	./vendor/bin/sail down
cache-clear:
	./vendor/bin/sail artisan cache:clear 
test:
	./vendor/bin/sail artisan test
config-clear:
	./vendor/bin/sail artisan config:clear 
