<?php
define('DEBUG', true);
function debug() {if (!DEBUG) return; foreach (func_get_args() as $sArgDebug) error_log(var_export($sArgDebug, true));}

fscanf(STDIN, "%d %d",
    $W, // number of columns.
    $H // number of rows.
);
$aMap = array();
for ($i = 0; $i < $H; ++$i) {
    // represents a line in the grid and contains W integers. Each integer represents one room of a given type.
    $LINE = stream_get_line(STDIN, 3*$W+1, "\n"); 
    $aMap[$i] = explode(' ', $LINE);
}
fscanf(STDIN, "%d",
    $EX // the coordinate along the X axis of the exit (not useful for this first mission, but must be read).
);

define('TOP', 'TOP');
define('LEFT', 'LEFT');
define('RIGHT', 'RIGHT');
define('BOTTOM', 'BOTTOM');

class Tile0 {public $aDir = array();}
class Tile1 {public $aDir = array(TOP => BOTTOM, LEFT => BOTTOM, RIGHT => BOTTOM);}
class Tile2 {public $aDir = array(LEFT => RIGHT, RIGHT => LEFT);}
class Tile3 {public $aDir = array(TOP => BOTTOM);}
class Tile4 {public $aDir = array(TOP => LEFT, RIGHT => BOTTOM);}
class Tile5 {public $aDir = array(TOP => RIGHT, LEFT => BOTTOM);}
class Tile6 {public $aDir = array(LEFT => RIGHT, RIGHT => LEFT);}
class Tile7 {public $aDir = array(TOP => BOTTOM, RIGHT => BOTTOM);}
class Tile8 {public $aDir = array(LEFT => BOTTOM, RIGHT => BOTTOM);}
class Tile9 {public $aDir = array(TOP => BOTTOM, LEFT => BOTTOM);}
class Tile10 {public $aDir = array(TOP => LEFT);}
class Tile11 {public $aDir = array(TOP => RIGHT);}
class Tile12 {public $aDir = array(RIGHT => BOTTOM);}
class Tile13 {public $aDir = array(LEFT => BOTTOM);}

// game loop
while (TRUE)
{
    fscanf(STDIN, "%d %d %s", $XI, $YI, $POS);
    $iTileType = $aMap[$YI][$XI];
    $sClassTile = 'Tile' . $iTileType;
    
    $oTile = new $sClassTile;
    $nextMove = $oTile->aDir[$POS];
    
    debug($POS, $iTileType, $nextMove);
    
    //$nextMove = LEFT, RIGHT or BOTTOM
    // One line containing the X Y coordinates of the room in which you believe Indy will be on the next turn.
    if ($nextMove === LEFT) {
        echo ($XI-1) . ' ' . $YI . "\n";
        continue;
    }
    if ($nextMove === RIGHT) {
        echo ($XI+1) . ' ' . $YI . "\n";
        continue;
    }
    if ($nextMove === BOTTOM) {
        echo $XI . ' ' . ($YI+1) . "\n";
        continue;
    }
}
?>