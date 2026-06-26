#! /bin/bash

docker compose up --build --remove-orphans -d
docker compose run api composer install
docker compose run api php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration
docker compose run api php bin/console messenger:setup-transports

