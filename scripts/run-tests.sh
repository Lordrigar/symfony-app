#!/usr/bin/env bash

# Print info message
function print {
    echo
    echo -e "\e[38;5;82m>>> $1"
    echo -e "\e[0m"
}

# Print error message
function error {
    echo
    echo -e "\e[31;5;82m>>> $1"
    echo -e "\e[0m"
}

docker-compose exec php bin/console cache:clear --env=test

print 'Tests starts...'

print 'Update database...'
./symfony-app/scripts/update-db.sh --test

print 'PHPUnit Unit tests...'
docker-compose exec php bin/phpunit --coverage-html ./tmp
if [[ $? != 0 ]]; then error 'Tests failed'; exit 1; fi

print 'Tests successfully ended'
exit 0