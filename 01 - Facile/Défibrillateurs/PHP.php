<?php
/**
 * Auto-generated code below aims at helping you parse
 * the standard input according to the problem statement.
 **/

fscanf(STDIN, "%s",
    $LON
);
fscanf(STDIN, "%s",
    $LAT
);
fscanf(STDIN, "%d",
    $N
);

$minDist = null;
$outName = null;
for ($i = 0; $i < $N; $i++)
{
    $DEFIB = stream_get_line(STDIN, 256, "\n");
    list($id, $name, $address, $phone, $longitude, $latitude) = explode(';', $DEFIB);
    
    $radLong = deg2rad(str_replace(',', '.', $longitude));
    $radLat = deg2rad(str_replace(',', '.', $latitude));
    $radUserLong = deg2rad(str_replace(',', '.', $LON));
    $radUserLat = deg2rad(str_replace(',', '.', $LAT));
    
    $x = ($radLong - $radUserLong) * cos(($radUserLat + $radLat)/2);
    $y = ($radLat - $radUserLat);
    $d = 6371*sqrt($x*$x + $y*$y); //$d >= 0
    
    if ($minDist === null || $d < $minDist) {
        $minDist = $d;
        $outName = $name;
    }
}

// Write an action using echo(). DON'T FORGET THE TRAILING \n
// To debug (equivalent to var_dump): error_log(var_export($var, true));

echo("$outName\n");
?>