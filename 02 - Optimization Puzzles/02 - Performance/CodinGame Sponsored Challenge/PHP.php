<?php
namespace Codingame_Sponsored_Challenge;

define('DEBUG', true);
function debug() {if (!DEBUG) return; foreach (func_get_args() as $sArgDebug) error_log(var_export($sArgDebug, true));}

define('PLAYER_A', 'A');
define('PLAYER_B', 'B');
define('PLAYER_C', 'C');
define('PLAYER_D', 'D');
define('PLAYER_ME', 'X');

// A => Get right
// B => No move
// C => Get up
// D => Get down
// E => Get left
define('RIGHT', "A\n");
define('STAY', "B\n");
define('UP', "C\n");
define('DOWN', "D\n");
define('LEFT', "E\n");

class Game
{
    public static $width;
    public static $height;

    public static function coordinatesToIndex($x, $y)
    {
        if ($x < 0) {
            throw new \InvalidArgumentException('X coordinate must be positive.');
        }
        if ($x > self::$width) {
            throw new \InvalidArgumentException(sprintf('X coordinate must be less than %s.', self::$width));
        }
        if ($y < 0) {
            throw new \InvalidArgumentException('Y coordinate must be positive.');
        }
        if ($y > self::$height) {
            throw new \InvalidArgumentException(sprintf('Y coordinate must be less than %s.', self::$height));
        }
        return $x + $y * self::$width;
    }

    public static function indexToCoordinates($i, $reversed = false)
    {
        if ($i < 0) {
            throw new \InvalidArgumentException('Index must be positive.');
        }
        if ($i > self::getMaxIndex()) {
            throw new \InvalidArgumentException(sprintf('Index must be less than %s.', self::getMaxIndex()));
        }

        $x = $i % self::$width;
        $y = floor($i / self::$width);

        if ($reversed) {
            return [$y, $x];
        }
        return [$x, $y];
    }

    public static function getMaxIndex()
    {
        return self::$width * self::$height;
    }
}

class Map
{
    private static $instance;

    public $width;
    public $height;

    /** @var Box[] */
    public $boxes;

    private function __construct()
    {
        fscanf(STDIN, "%d", Game::$height);
        fscanf(STDIN, "%d", Game::$width);
        $this->initBoxes();
    }

    public static function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = new Map();
        }
        return self::$instance;
    }

    public function getBox($x, $y)
    {
        return $this->boxes[Game::coordinatesToIndex($x, $y)];
    }

    public function initBoxes()
    {
        for ($y=0; $y<=Game::$height; ++$y) {
            for ($x=0; $x<=Game::$width; ++$x) {
                $this->boxes[Game::coordinatesToIndex($x, $y)] = new Box($x, $y);
            }
        }
    }

    public function resetPlayersPositions()
    {
        for ($i=0, $iMax=Game::getMaxIndex(); $i<=$iMax; ++$i) {
            $this->boxes[$i]->resetBox();
        }
    }

    public function setBoxValue($x, $y, $value)
    {
        $this->getBox($x, $y)->update($value);
        return $this;
    }

    public function getBoxValue($x, $y)
    {
        return $this->getBox($x, $y)->currentValue;
    }

    /**
     * @param Box $oBox
     * @return Box[]
     */
    public function getBoxesAround(Box $oBox)
    {
        list($x, $y) = $oBox->getCoordinates();
        return [
            $this->getBox($x, $y-1), //UP
            $this->getBox($x+1, $y), //RIGHT
            $this->getBox($x, $y+1), //DOWN
            $this->getBox($x-1, $y), //LEFT
        ];
    }

    public function debug()
    {
        $s = PHP_EOL;
        for ($y=0; $y<=Game::$height; ++$y) {
            for ($x=0; $x<=Game::$width; ++$x) {
                $s .= $this->getBoxValue($x, $y);
            }
            $s .= PHP_EOL;
        }
        debug($s);
    }

    public function setAround($x, $y, $aChars)
    {
        list($up, $down, $left, $right) = $aChars;
        $this->getBoxValue($x, $y-1) !== '?' ?: $this->setBoxValue($x, $y-1, $up);
        $this->getBoxValue($x, $y+1) !== '?' ?: $this->setBoxValue($x, $y+1, $down);
        $this->getBoxValue($x-1, $y) !== '?' ?: $this->setBoxValue($x-1, $y, $left);
        $this->getBoxValue($x+1, $y) !== '?' ?: $this->setBoxValue($x+1, $y, $right);

        return $this;
    }
}

class Box
{
    public static $allPlayers = [PLAYER_A, PLAYER_B, PLAYER_C, PLAYER_D, PLAYER_ME];
    public static $allPlayersButMe = [PLAYER_A, PLAYER_B, PLAYER_C, PLAYER_D];

    private $x;
    private $y;

    public $absoluteValue;
    public $currentValue;

    const UNKNOWN = '?';
    const WALL = '#';
    const CLEAN = '_';

    public $bVisited = false;
    public $bCrossing = false;

    public function __construct($x, $y)
    {
        $this->x = $x;
        $this->y = $y;

        $this->absoluteValue = null;
        $this->currentValue = self::UNKNOWN;
    }

    public function resetBox()
    {
        //Reset the move of a player but not me and that's enough for them.
        if (in_array($this->currentValue, self::$allPlayersButMe)) {
            $this->update(self::CLEAN);
            return $this;
        }

        //If the box was never visited and I just visit it for the first time, set properties on that box.
        if (!$this->bVisited && $this->currentValue === PLAYER_ME) {
            $this->bVisited = true;
            //Check, in this case, if the boxes around are available to find if this box is a crossing.
            //If more than 2 boxes are clean, there's a crossing on the current box.
            $iNbWayOut = 0;
            foreach (Map::getInstance()->getBoxesAround($this) as $oBox) {
                $iNbWayOut += (int)(null !== $oBox && !$oBox->isWall());
            }
            $this->bCrossing = $iNbWayOut > 2;
        }

        //When visited, simply update the value
        if ($this->bVisited) {
            $this->update(self::CLEAN);
        }
        return $this;
    }

    public function update($newValue)
    {
        $this->absoluteValue = $this->currentValue;
        $this->currentValue = $newValue;
        return $this;
    }

    public function isWall()
    {
        return $this->absoluteValue === self::WALL || $this->currentValue === self::WALL;
    }

    public function getCoordinates()
    {
        return [$this->x, $this->y];
    }
}

$oMap = Map::getInstance();

fscanf(STDIN, "%d", $nbPlayers);
$sPlayers = "ABCD";

while (1) {
    fscanf(STDIN, "%s", $a); //Box to the up
    fscanf(STDIN, "%s", $b); //Box to the right
    fscanf(STDIN, "%s", $c); //Box to the bottom
    fscanf(STDIN, "%s", $d); //Box to the left

    $oMap->resetPlayersPositions();

    for ($i = 0; $i < $nbPlayers-1; ++$i) {
        $sPlayer = $sPlayers[$i];

        fscanf(STDIN, "%d %d", $x, $y);
        $oMap->setBoxValue($x, $y, $sPlayer);

        debug($sPlayer . ' (' . $x . ';' . $y . ')');
    }

    //Me
    fscanf(STDIN, "%d %d", $x, $y);
    $oMap->setBoxValue($x, $y, 'X');
    $oMap->setAround($x, $y, [$a, $c, $d, $b]);
    debug('Me (' . $x . ';' . $y . ')');

    $oMap->debug();

    if ($oMap->getBoxValue($x, $y+1) === '_') {
        echo DOWN;
    } elseif ($oMap->getBoxValue($x-1, $y) === '_') {
        echo LEFT;
    } elseif ($oMap->getBoxValue($x+1, $y) === '_') {
        echo RIGHT;
    } elseif ($oMap->getBoxValue($x, $y-1) === '_') {
        echo UP;
    } else {
        echo STAY;
    }

}