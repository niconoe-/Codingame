<?php
/**
 * Auto-generated code below aims at helping you parse
 * the standard input according to the problem statement.
 **/

fscanf(STDIN, "%d %d",
    $W, // width of the building.
    $H // height of the building.
);
fscanf(STDIN, "%d",
    $N // maximum number of turns before game over.
);
fscanf(STDIN, "%d %d",
    $X0,
    $Y0
);

$x = $X0;
$y = $Y0;
$zoneXi = 0;
$zoneYi = 0;
$zoneXj = $W-1;
$zoneYj = $H-1;

// game loop
while (TRUE)
{
    fscanf(STDIN, "%s",
        $BOMB_DIR // the direction of the bombs from batman's current location (U, UR, R, DR, D, DL, L or UL)
    );
    
    if ($BOMB_DIR === 'U') {
        $areaXa = $x;
        $areaYa = $zoneYi;
        $areaXb = $x;
        $areaYb = $y-1;
    } elseif ($BOMB_DIR === 'UR') {
        $areaXa = $x+1;
        $areaYa = $zoneYi;
        $areaXb = $zoneXj;
        $areaYb = $y-1;
    } elseif ($BOMB_DIR === 'R') {
        $areaXa = $x+1;
        $areaYa = $y;
        $areaXb = $zoneXj;
        $areaYb = $y;
    } elseif ($BOMB_DIR === 'DR') {
        $areaXa = $x+1;
        $areaYa = $y+1;
        $areaXb = $zoneXj;
        $areaYb = $zoneYj;
    } elseif ($BOMB_DIR === 'D') {
        $areaXa = $x;
        $areaYa = $y+1;
        $areaXb = $x;
        $areaYb = $zoneYj;
    } elseif ($BOMB_DIR === 'DL') {
        $areaXa = $zoneXi;
        $areaYa = $y+1;
        $areaXb = $x-1;
        $areaYb = $zoneYj;
    } elseif ($BOMB_DIR === 'L') {
        $areaXa = $zoneXi;
        $areaYa = $y;
        $areaXb = $x-1;
        $areaYb = $y;
    } elseif ($BOMB_DIR === 'UL') {
        $areaXa = $zoneXi;
        $areaYa = $zoneYi;
        $areaXb = $x-1;
        $areaYb = $y-1;
    }
    
    //Dichotomic move through the area.
    $areaWidth = $areaXb-$areaXa;
    $areaHeight = $areaYb-$areaYa;
    $areaXo = $areaXa + ceil($areaWidth/2);
    $areaYo = $areaYa + ceil($areaHeight/2);
    
    //Redefine the new area
    $zoneXi = $areaXa;
    $zoneYi = $areaYa;
    $zoneXj = $areaXb;
    $zoneYj = $areaYb;
    
    //Redefine the Batman position
    $x = $areaXo;
    $y = $areaYo;
    
    // Write an action using echo(). DON'T FORGET THE TRAILING \n
    // To debug (equivalent to var_dump): error_log(var_export($var, true));

    echo("$x $y\n"); // the location of the next window Batman should jump to.
}
?>