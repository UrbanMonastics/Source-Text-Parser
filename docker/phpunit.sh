#!/bin/bash

# run the PHPUnit tests
docker exec -it sourceparser_nginx_php ./vendor/bin/phpunit --testdox test