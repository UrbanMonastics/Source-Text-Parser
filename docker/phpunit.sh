#!/bin/bash

# run the PHPUnit tests
docker exec -it sourcetextparser_nginx_php ./vendor/bin/phpunit --testdox test