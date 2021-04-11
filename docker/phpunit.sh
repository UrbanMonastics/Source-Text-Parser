#!/bin/bash

# run the PHPUnit tests
docker exec -it textformater_nginx_php ./vendor/bin/phpunit --testdox test