#!/usr/bin/env bash

docker-compose up -d
docker exec -it orderchallenge_php php artisan migrate
