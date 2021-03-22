#!/bin/bash

# update all our submodules using the remote branch
#git submodule update --remote

# update composer libraries
docker exec -it textformater_nginx_php composer update