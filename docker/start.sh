#!/bin/bash

# validate our current working directory has no spaces
D=`pwd`;
if [[ "$D" =~ " " ]]
then
	echo "You have a space in your pwd. Please cd into a path without any spaces."
	exit 1
fi

# start in the background
docker-compose up -d

echo
echo "You can connect from your development (host) machine to these services:"
echo "SourceParser:    http://localhost:8080/"
echo
