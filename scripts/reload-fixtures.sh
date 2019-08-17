#!/usr/bin/env bash

docker-compose exec php bin/console cache:clear

echo 'Recreate database...'
# Drop and create new database
docker-compose exec php bin/console doctrine:database:drop --force
docker-compose exec php bin/console doctrine:database:create

# Update database before we have initial migration created
docker-compose exec php bin/console doctrine:schema:update --force

# Run migrations/Update schema
docker-compose exec php bin/console doctrine:migrations:migrate -n

# Validate mapping for entities
docker-compose exec php bin/console doctrine:schema:validate

echo 'Reload fixtures...'
docker-compose exec php bin/console doctrine:fixtures:load --append -n