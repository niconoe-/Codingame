<?php
/**
 * Auto-generated code below aims at helping you parse
 * the standard input according to the problem statement.
 **/

fscanf(STDIN, "%d",
    $N // the number of points used to draw the surface of Mars.
);
for ($i = 0; $i < $N; $i++)
{
    fscanf(STDIN, "%d %d",
        $LAND_X, // X coordinate of a surface point. (0 to 6999)
        $LAND_Y // Y coordinate of a surface point. By linking all the points together in a sequential fashion, you form the surface of Mars.
    );
}

$lU = 0;

// game loop
while (TRUE)
{
    fscanf(STDIN, "%d %d %d %d %d %d %d",
        $X,
        $Y,
        $HS, // the horizontal speed (in m/s), can be negative.
        $VS, // the vertical speed (in m/s), can be negative.
        $F, // the quantity of remaining fuel in liters.
        $R, // the rotation angle in degrees (-90 to 90).
        $P // the thrust power (0 to 4).
    );
    
    $a = 0;
    $b = 9;
    $c = 9;
    $d = 12;
    $e = 9;

    // Write an action using echo(). DON'T FORGET THE TRAILING \n
    // To debug (equivalent to var_dump): error_log(var_export($var, true));
    if ($lU >= ($a+$b+2*$c+3*$d)) {
        $oP = 4;
    } elseif ($lU >= ($a+$b+2*$c)) {
        $oP = 3;
    } elseif ($lU >= ($a+$b)) {
        $oP = 2;
    } elseif ($lU >= $a) {
        $oP = 1;
    } else {
        $lU++;
        $oP = 0;
    }


    $lU += $oP;
    echo("0 $oP\n"); // R P. R is the desired rotation angle. P is the desired thrust power.
}
?>