<?php
$max = PHP_INT_MAX;
$value = null;
for (fscanf(STDIN, "%d", $N), $i = 0; $i < $N; $i++) {
    fscanf(STDIN, "%s", $t);
    if (strtotime($t) < $max) {
        $max = strtotime($value = $t);
    }
}

echo $value;