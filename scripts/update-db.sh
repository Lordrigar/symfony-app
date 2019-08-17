#!/usr/bin/env bash

# Rerun all the updates/fixtures/migrations for DB
#
# Script could be run both from the root of the project and within
# scripts directory also.
#
# Usage:
#       ./scripts/update-db.sh OPTIONS
#
# Options:
#       --dev     Use dev environment
#       --test    Use test environment [default]
#==============================================================================

__ENV__=test

for arg in "$@"
do
    case ${arg} in
        --test)
            __ENV__=test
            shift
        ;;
        --dev)
            __ENV__=dev
            shift
        ;;
    esac
done

# Drop and create sqlite database
docker-compose exec php bin/console doctrine:database:drop --env=${__ENV__} --force
docker-compose exec php bin/console doctrine:database:create --env=${__ENV__}

# Run schema updates and fixtures
if [ ${__ENV__} = 'test' ]; then
    docker-compose exec php bin/console doctrine:schema:update --force --env=test
fi

docker-compose exec php bin/console doctrine:migrations:migrate --env=${__ENV__} -n
docker-compose exec php bin/console doctrine:fixtures:load --append --env=${__ENV__} -n

exit 0