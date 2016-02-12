<?php
namespace Codingame_Sponsored_Challenge;

define('DEBUG', true);
function debug() {if (!DEBUG) return; foreach (func_get_args() as $sArgDebug) error_log(var_export($sArgDebug, true));}

define('PLAYER_A', 'A');
define('PLAYER_B', 'B');
define('PLAYER_C', 'C');
define('PLAYER_D', 'D');
define('PLAYER_ME', 'X');

class Map
{
    private static $instance;

    public static $allPlayers = [PLAYER_A, PLAYER_B, PLAYER_C, PLAYER_D, PLAYER_ME];

    public $width;
    public $height;

    public $boxes;

    private function __construct()
    {
        fscanf(STDIN, "%d", $this->height);
        fscanf(STDIN, "%d", $this->width);
        $this->initBoxes();
    }

    public static function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = new Map();
        }
        return self::$instance;
    }

    public function initBoxes()
    {
        for ($y=1; $y<=$this->height; ++$y) {
            $this->boxes[$y] = array_fill(1, $this->width, '?');
        }
    }

    public function resetPlayersPositions()
    {
        for ($y=1; $y<=$this->height; ++$y) {
            for ($x=1; $x<=$this->width; ++$x) {
                if (in_array($this->boxes[$y][$x], self::$allPlayers)) {
                    $this->boxes[$y][$x] = '_';
                }
            }
        }
    }

    public function setBox($x, $y, $value)
    {
        $this->boxes[$y][$x] = $value;
        return $this;
    }

    public function debug()
    {
        $s = PHP_EOL;
        for ($y=1; $y<=$this->height; ++$y) {
            for ($x=1; $x<=$this->width; ++$x) {
                $s .= $this->boxes[$y][$x];
            }
            $s .= PHP_EOL;
        }
        debug($s);
    }
}

$oMap = Map::getInstance();

fscanf(STDIN, "%d", $nbPlayers);

debug('INIT');
debug('width = ' . $oMap->width);
debug('height = ' . $oMap->height);
debug('nbPlayers = ' . $nbPlayers);
$sPlayers = "ABCD";

while(1) {
    fscanf(STDIN, "%s", $a); //Box to the up
    fscanf(STDIN, "%s", $b); //Box to the right
    fscanf(STDIN, "%s", $c); //Box to the bottom
    fscanf(STDIN, "%s", $d); //Box to the left

    $oMap->resetPlayersPositions();

    debug('LOOP');
    debug('a = ' . $a);
    debug('b = ' . $b);
    debug('c = ' . $c);
    debug('d = ' . $d);

    for ($i = 0; $i < $nbPlayers-1; ++$i) {
        $sPlayer = $sPlayers[$i];

        fscanf(STDIN, "%d %d", $x, $y);
        $oMap->setBox($x, $y, $sPlayer);

        debug('Coordinates Player ' . $sPlayer);
        debug('x = ' . $x);
        debug('y = ' . $y);
    }

    //Me ?
    fscanf(STDIN, "%d %d", $x, $y);
    $oMap->setBox($x, $y, 'X');
    debug('Coordinates Me X');
    debug('x = ' . $x);
    debug('y = ' . $y);

    //todo: Add clues of lines 87 to 90 in the map, in real-time

    $oMap->debug();

    echo("A\n");


    // A => Get right
    // B => No move
    // C => Get up
    // D => Get down
    // E => Get left
}