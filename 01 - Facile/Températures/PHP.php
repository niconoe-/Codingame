<?php
/**
 * Auto-generated code below aims at helping you parse
 * the standard input according to the problem statement.
 **/

fscanf(STDIN, "%d",
    $N // the number of temperatures to analyse
);
$TEMPS = stream_get_line(STDIN, 256, "\n"); // the N temperatures expressed as integers ranging from -273 to 5526

if ($N === 0) {
    echo "0\n";
    exit;
}

$aValues = explode(' ', $TEMPS);
$closerZero = $aValues[0];
for ($i = 1; $i < $N; ++$i) {
    if (abs($closerZero) > abs($aValues[$i])) {
        $closerZero = $aValues[$i];
    } elseif (abs($closerZero) === abs($aValues[$i])) {
        //Take the maximum because if negative and positive value are both closes to 0, take the positive value.
        $closerZero = max($closerZero, $aValues[$i]);
    }
}
// Write an action using echo(). DON'T FORGET THE TRAILING \n
// To debug (equivalent to var_dump): error_log(var_export($var, true));

echo("$closerZero\n");
?>