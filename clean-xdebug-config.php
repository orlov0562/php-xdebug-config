#!/bin/bash

service_exists() {
    local n=$1
    if [[ $(systemctl list-units --all -t service --full --no-legend "$n.service" | cut -f1 -d' ') == $n.service ]]; then
        return 0
    else
        return 1
    fi
}

cleanup() {
    local n=$1

    echo "Checking PHP $n"

    if test -f "/etc/php/$n/cli/conf.d/20-xdebug-cli.ini"; then
        echo "20-xdebug-cli.ini present"
        if test -f "/etc/php/$n/cli/conf.d/20-xdebug.ini"; then
            echo "20-xdebug.ini present, removing.."
            rm "/etc/php/$n/cli/conf.d/20-xdebug.ini"
            if service_exists php$n-fpm; then
                echo "restarting service php$n-fpm"
                systemctl restart php$n-fpm
            else
                echo "service php$n-fpm not found"
            fi
        else
            echo "20-xdebug.ini not found"
        fi
    else
        echo "20-xdebug-cli.ini not found"
    fi

    echo '--------'
}

cleanup 5.6;
cleanup 7.0;
cleanup 7.1;
cleanup 7.2;
cleanup 7.3;
cleanup 7.4;
cleanup 8.0;
