#!/bin/sh

user=www-data
command=''
while [ $# -gt 0 ]; do
    case $1 in
        -u|--user) user=$2; shift ;;
        *) command="$command $1" ;;
    esac; shift
done

docker-compose exec --user $user -e XDEBUG_MODE=off app php -d memory_limit=-1 /usr/bin/composer $command
