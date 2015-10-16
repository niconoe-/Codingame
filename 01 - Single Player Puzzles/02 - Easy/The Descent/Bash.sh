#!/usr/bin/env sh

while true; do
    read spaceX spaceY
    iHighest=-1
    vHighest=0

    for (( i=0; i<8; i++ )); do
        # mountainH: represents the height of one mountain, from 9 to 0. Mountain heights are provided from left to right.
        read mountainH
        if ((${vHighest} <= ${mountainH}))
            then
            iHighest=${i}
            vHighest=${mountainH}
        fi
    done

    if ((${spaceX} == ${iHighest}))
        then
        echo "FIRE"
    else
        echo "HOLD"
    fi
done
