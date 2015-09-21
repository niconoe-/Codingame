<?php
/**
 * Auto-generated code below aims at helping you parse
 * the standard input according to the problem statement.
 **/

fscanf(STDIN, "%d %d", $N, $X);
for ($i=1; $i<strlen($N); ++$i) {
    $a = substr($N, 0, $i);
    $b = substr($N, $i);

    if (($a + $b) == $X) {
        echo $a . '+' . $b;
        exit;
    }
    if (($a - $b) == $X) {
        echo $a . '-' . $b;
        exit;
    }
    if (($a * $b) == $X) {
        echo $a . '*' . $b;
        exit;
    }
    if (($a / $b) == $X) {
        echo $a . '/' . $b;
        exit;
    }
}

if ($X < 0) {
    echo $X;
} else {
    echo '+' . $X;
}