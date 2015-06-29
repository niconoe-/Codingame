#!/usr/bin/env bash

read N
ARRAY=()
for (( i=0; i<N; i++ )); do
    read Pi
    ARRAY[i]=${Pi}
done

readarray -t SARRAY < <(for a in "${ARRAY[@]}"; do echo "$a"; done | sort -n)
minDiff=10000000
prev=${SARRAY[0]}
for (( i=1; i<N; i++ )); do
    curr=${SARRAY[i]}
    diff=$(($curr-$prev))
    if [ ${diff} -lt ${minDiff} ]
    then
        minDiff=${diff}
    fi
    prev=${curr}
done

echo ${minDiff}