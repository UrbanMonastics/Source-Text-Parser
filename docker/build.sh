#!/bin/bash

# build our project
docker compose build

# start in the background
docker-compose up -d

# update composer
docker exec -it sourcetextparser_nginx_php composer selfupdate --2

# install composer libraries
docker exec -it sourcetextparser_nginx_php composer install