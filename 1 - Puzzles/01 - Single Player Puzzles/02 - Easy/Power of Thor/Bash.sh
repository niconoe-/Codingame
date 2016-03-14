#!/usr/bin/env sh
read LX LY TX TY

# game loop
while true; do
    dY=$((TY-LY))
    dX=$((TX-LX))
    s=''

    if ((${dY} > 0))
        then
        s=${s}"N"
        TY=$((TY-1))
    else
        if ((${dY} < 0))
            then
            s=${s}"S"
            TY=$((TY+1))
        fi
    fi

    if ((${dX} > 0))
        then
        s=${s}"W"
        TX=$((TX-1))
    else
        if ((${dX} < 0))
            then
            s=${s}"E"
            TX=$((TX+1))
        fi
    fi

    echo ${s}
done