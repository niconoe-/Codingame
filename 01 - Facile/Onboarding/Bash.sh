#!/usr/bin/env sh
while true; do
    read enemy1
    read dist1
    read enemy2
    read dist2

    if (($dist1 <= $dist2))
        then
        echo ${enemy1}
    else
        echo ${enemy2}
    fi
done