<?php
/**
 * Auto-generated code below aims at helping you parse
 * the standard input according to the problem statement.
 **/

fscanf(STDIN, "%d %d %d %d",
    $LX, // the X position of the light of power
    $LY, // the Y position of the light of power
    $TX, // Thor's starting X position
    $TY // Thor's starting Y position
);

// game loop
while (TRUE)
{
    
    $lastDirection = isset($lastDirection) ? $lastDirection : null;
    fscanf(STDIN, "%d",
        $E // The level of Thor's remaining energy, representing the number of moves he can still make.
    );
    
    if ($LY === $TY) {
        echo ($LX < $TX) ? "W\n" : "E\n";
        continue;
    } 
    if ($LX === $TX) {
        echo ($LY < $TY) ? "N\n" : "S\n";
        continue;
    }
    
    //Not a strait strategy
    if ($lastDirection === null) {
        $CY = $TY;
        $CX = $TX;
    } else {
        $aTemp = str_split($lastDirection);
        if (count($aTemp) === 2) {
            $yDir = $aTemp[0];
            $xDir = $aTemp[1];
        } elseif ($lastDirection === 'N' || $lastDirection === 'S') {
            $yDir = $lastDirection;
            $xDir = null;
        } else {
            $yDir = null;
            $xDir = $lastDirection;
        }
        
        $yMvt = ($yDir === 'N' ? -1 : ($yDir === 'S' ? 1 : 0));
        $xMvt = ($xDir === 'W' ? -1 : ($xDir === 'E' ? 1 : 0));
        
        $CY += $yMvt;
        $CX += $xMvt;
    }
    
    $direction = null;
    if ($CY > $LY) {
        $direction .= "N";
    } elseif ($CY < $LY) {
        $direction .= "S";
    }
    if ($CX > $LX) {
        $direction .= "W";
    } elseif ($CX < $LX) {
        $direction .= "E";
    }
    $lastDirection = $direction;
    $direction .= "\n";
    echo $direction;
    

    // Write an action using echo(). DON'T FORGET THE TRAILING \n
    // To debug (equivalent to var_dump): error_log(var_export($var, true));

    //echo("E\n"); // A single line providing the move to be made: N NE E SE S SW W or NW
}
?>