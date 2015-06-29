<?php
namespace Indiana_Level3;

define('DEBUG', true);
function debug() {if (!DEBUG) return; foreach (func_get_args() as $sArgDebug) error_log(var_export($sArgDebug, true));}

define('TOP', 'TOP');
define('LEFT', 'LEFT');
define('RIGHT', 'RIGHT');
define('BOTTOM', 'BOTTOM');
define('HALF', 'HALF');
define('NONE', 'NONE');
define('WAIT', 'WAIT');

/**
 * Class Map
 *
 * @package   Indiana_Level3
 * @author Nicolas (niconoe) Giraud <nicolas.giraud.dev@gmail.com>
 * @copyright Copyright © 2015, Nicolas Giraud
 */
class Map
{
    /**
     * @var Tile[][] $aMap 2 dimensional array that contains Tile
     */
    public $aMap;

    /**
     * @var int $h The height of the map
     */
    public $h;

    /**
     * @var int $w The width of the map
     */
    public $w;

    /**
     * @var int $xExit The X coordinate of the exit. The Y coordinate is the height - 1.
     */
    public $xExit;

    /**
     * @var Indiana $oIndiana Our hero instance!
     */
    public $oIndiana = null;

    /**
     * @var Boulder[] $aBoulders Array of all boulders currently defined on the map.
     */
    public $aBoulders = [];

    /**
     * Reads the initialisation input to define the map
     */
    public function __construct()
    {
        //The two firsts parameters are width and height of the map.
        fscanf(STDIN, "%d %d", $this->w, $this->h);

        //For each row, the input is the type of tile. Negative value if rotation is blocked.
        for ($i = 0; $i < $this->h; ++$i) {
            $aTypeTile = explode(' ', stream_get_line(STDIN, 3*$this->w+1, "\n"));
            foreach ($aTypeTile as $j => $iTile) {
                $this->aMap[$i][$j] = Tile::createTile($iTile, $j, $i);
            }
        }

        // the coordinate along the X axis of the exit
        fscanf(STDIN, "%d", $this->xExit);
    }

    /**
     * Set Indiana onto the map.
     * @param \Indiana_Level3\Indiana $oIndiana
     * @return $this
     */
    public function setIndiana(Indiana $oIndiana)
    {
        $this->oIndiana = $oIndiana;
        return $this;
    }

    public function setBoulders()
    {
        //Manage boulders
        $this->aBoulders = [];
        fscanf(STDIN, "%d", $R);
        for ($i=0; $i<$R; ++$i) {
            $this->aBoulders[] = new Boulder();
        }
    }

    /**
     * @return \Indiana_Level3\Boulder[]
     */
    public function getBoulders()
    {
        return $this->aBoulders;
    }

    /**
     * Return the Tile object at the coordinate asked in parameter
     * @param int $x The X coordinate of the map
     * @param int $y The Y coordinate of the map
     * @return \Indiana_Level3\Tile
     */
    public function getTile($x, $y)
    {
        return $this->aMap[$y][$x];
    }

    /**
     * Return the next tile depending of the direction Indiana or Boulder is going
     * @param int $x The X coordinate of the moving element
     * @param int $y The Y coordinate of the moving element
     * @param string $dir The movement. Can be LEFT, RIGHT or BOTTOM.
     * @return \Indiana_Level3\Tile|null
     */
    public function getNextTile($x, $y, $dir)
    {
        if ($dir === LEFT) {
            return $this->aMap[$y][$x-1];
        } elseif ($dir === RIGHT) {
            return $this->aMap[$y][$x+1];
        } elseif ($dir === BOTTOM) {
            return $this->aMap[$y+1][$x];
        }
        //TOP isn't possible as Indiana can't get up.
        return null;
    }

    /**
     * Update the map by doing the rotation.
     * @param \Indiana_Level3\Tile $oTile
     * @param string $sRotation
     * @return $this
     */
    public function updateTile(Tile $oTile, $sRotation)
    {
        $oNewTile = $oTile->turn($sRotation);
        $oNewTile->x = $oTile->x;
        $oNewTile->y = $oTile->y;
        //This tile is able to rotate because we just rotated it
        $oNewTile->bRot = true;
        $this->aMap[$oNewTile->y][$oNewTile->x] = $oNewTile;
        return $this;
    }

    /**
     * @param $dir
     * @return string
     */
    public static function reverse($dir) {
        if ($dir === LEFT) {return RIGHT;}
        if ($dir === RIGHT) {return LEFT;}
        if ($dir === TOP) {return BOTTOM;}
        if ($dir === BOTTOM) {return TOP;}
        return null;
    }
}

/**
 * Abstract Class Tile
 *
 * @package   Indiana_Level3
 * @author Nicolas (niconoe) Giraud <nicolas.giraud.dev@gmail.com>
 * @copyright Copyright © 2015, Nicolas Giraud
 */
abstract class Tile
{
    public $aPossibilities = [];
    public $bRot = false;
    public $x;
    public $y;

    /**
     * @param $dir
     * @return mixed
     */
    abstract public function turn($dir);

    /**
     * Create a tile depending of the type and return it.
     * @param int $iTile The type of the tile. From 0 to 13 included, it defines the format of the tile.
     *                   If negative, impossible to rotate
     * @param int $x The X coordinate of the tile
     * @param int $y The Y coordinate of the tile
     * @return Tile
     */
    public static function createTile($iTile, $x, $y)
    {
        $sClassTile = __CLASS__ . abs($iTile);
        $oTile = new $sClassTile;
        if ($iTile > 0) {
            $oTile->bRot = true;
        }
        $oTile->x = $x;
        $oTile->y = $y;
        return $oTile;
    }

    /**
     * Return the next Tile depending on the direction
     * @param \Indiana_Level3\Map $oMap The Map object
     * @param string $sDir The direction to follow
     * @return \Indiana_Level3\Tile The next tile
     */
    public function getNextTile(Map $oMap, $sDir)
    {
        return $oMap->getNextTile($this->x, $this->y, $sDir);
    }

    /**
     * Return the mandatory direction from a tile. This have to exists and only 1 value can be returned.
     * @param string $dir The part of the tile we're entering. Can be TOP, LEFT or RIGHT.
     * @return string The part of the tile we're leaving
     */
    public function findDirection($dir)
    {
        return $this->aPossibilities[$dir][NONE];
    }

    /**
     * Return all possibilities of rotation of a tile depending on the enter into the tile
     * @param string $entering The part of the tile we're entering. Can be TOP, LEFT or RIGHT.
     * @return array All possibilities of leaving depending on the chosen rotation.
     */
    public function getPossibilities($entering)
    {
        $aPossibilities = $this->aPossibilities[$entering];
        //If we don't have time to make a half turn, unset the possibilities
        if (Main::$iStepAhead === 0) {
            unset($aPossibilities[HALF]);
        }
        $aPossibilities = array_filter($aPossibilities);

        //Unset all possibilities that make Indiana against a Tile0 or outside the map
        foreach ($aPossibilities as $sRotation => $sOutput) {
            if (
                ($this->x === 0 && $sOutput === LEFT) || //Outside the map by left
                ($this->x === Main::$oMap->w-1 && $sOutput === RIGHT) || //Outside the map by right
                ($this->y === Main::$oMap->h-1 && $sOutput === BOTTOM) //Outside the map by bottom
            ) {
                unset($aPossibilities[$sRotation]);
                continue;
            }

            $oNextTile = $this->getNextTile(Main::$oMap, $sOutput);
            if ($oNextTile instanceof Tile0) {
                unset($aPossibilities[$sRotation]);
            }
        }

        //If the HALF, LEFT or RIGHT is the same than the NONE, keep the NONE to have an step advantage
        if (!isset($aPossibilities[NONE])) {
            return $aPossibilities;
        }
        foreach ([HALF, LEFT, RIGHT] as $sRot) {
            if (isset($aPossibilities[$sRot]) && $aPossibilities[$sRot] === $aPossibilities[NONE]) {
                unset($aPossibilities[$sRot]);
            }
        }

        //If the LEFT is same than the RIGHT, keep the LEFT to avoid double.
        if (isset($aPossibilities[RIGHT], $aPossibilities[LEFT]) && $aPossibilities[RIGHT] === $aPossibilities[LEFT]) {
            unset($aPossibilities[RIGHT]);
        }

        return $aPossibilities;
    }

    /**
     * @param $entering
     * @return array
     */
    public function getPossibilitiesToBlock($entering)
    {
        $aPossibilities = $this->aPossibilities[$entering];
        //If we don't have time to make a half turn, unset the possibilities
        if (Main::$iStepAhead === 0) {
            unset($aPossibilities[HALF]);
        }
        $aBlocked = [];
        foreach ($aPossibilities as $sRot => $sWay) {
            if (is_null($sWay)) {
                $aBlocked[] = $sRot;
            }
        }
        return array_unique($aBlocked);
    }

    /**
     * Return the coordinate of the tile on the map.
     * @return int[]
     */
    public function getCoordinate()
    {
        return [$this->x, $this->y];
    }

    /**
     * Return if the tile is the exit
     * @param \Indiana_Level3\Map $oMap
     * @return bool
     */
    public function isExit(Map $oMap)
    {
        list($x, $y) = $this->getCoordinate();
        return ($y === $oMap->h-1 && $x === $oMap->xExit);
    }
}

/**
 * Class Tile0
 *
 * @package   Indiana_Level3
 * @author Nicolas (niconoe) Giraud <nicolas.giraud.dev@gmail.com>
 * @copyright Copyright © 2015, Nicolas Giraud
 */
class Tile0 extends Tile {
    public $aPossibilities = [
        TOP => [NONE => null, LEFT => null, RIGHT => null, HALF => null],
        LEFT => [NONE => null, LEFT => null, RIGHT => null, HALF => null],
        RIGHT => [NONE => null, LEFT => null, RIGHT => null, HALF => null],
    ];

    /**
     * @param $dir
     * @return \Indiana_Level3\Tile0
     */
    public function turn($dir) {
        return ($dir === LEFT) ? (new Tile0) : (new Tile0);
    }
}

/**
 * Class Tile1
 *
 * @package   Indiana_Level3
 * @author Nicolas (niconoe) Giraud <nicolas.giraud.dev@gmail.com>
 * @copyright Copyright © 2015, Nicolas Giraud
 */
class Tile1 extends Tile {
    public $aPossibilities = [
        TOP => [NONE => BOTTOM, LEFT => BOTTOM, RIGHT => BOTTOM, HALF => BOTTOM],
        LEFT => [NONE => BOTTOM, LEFT => BOTTOM, RIGHT => BOTTOM, HALF => BOTTOM],
        RIGHT => [NONE => BOTTOM, LEFT => BOTTOM, RIGHT => BOTTOM, HALF => BOTTOM],
    ];

    /**
     * @param $dir
     * @return \Indiana_Level3\Tile1
     */
    public function turn($dir) {
        return ($dir === LEFT) ? (new Tile1) : (new Tile1);
    }
}

/**
 * Class Tile2
 *
 * @package   Indiana_Level3
 * @author Nicolas (niconoe) Giraud <nicolas.giraud.dev@gmail.com>
 * @copyright Copyright © 2015, Nicolas Giraud
 */
class Tile2 extends Tile {
    public $aPossibilities = [
        TOP => [NONE => null, LEFT => BOTTOM, RIGHT => BOTTOM, HALF => null],
        LEFT => [NONE => RIGHT, LEFT => null, RIGHT => null, HALF => RIGHT],
        RIGHT => [NONE => LEFT, LEFT => null, RIGHT => null, HALF => LEFT],
    ];

    /**
     * @param $dir
     * @return \Indiana_Level3\Tile3
     */
    public function turn($dir) {
        return ($dir === LEFT) ? (new Tile3) : (new Tile3);
    }
}

/**
 * Class Tile3
 *
 * @package   Indiana_Level3
 * @author Nicolas (niconoe) Giraud <nicolas.giraud.dev@gmail.com>
 * @copyright Copyright © 2015, Nicolas Giraud
 */
class Tile3 extends Tile {
    public $aPossibilities = [
        TOP => [NONE => BOTTOM, LEFT => null, RIGHT => null, HALF => BOTTOM],
        LEFT => [NONE => null, LEFT => RIGHT, RIGHT => RIGHT, HALF => null],
        RIGHT => [NONE => null, LEFT => LEFT, RIGHT => LEFT, HALF => null],
    ];

    /**
     * @param $dir
     * @return \Indiana_Level3\Tile2
     */
    public function turn($dir) {
        return ($dir === LEFT) ? (new Tile2) : (new Tile2);
    }
}

/**
 * Class Tile4
 *
 * @package   Indiana_Level3
 * @author Nicolas (niconoe) Giraud <nicolas.giraud.dev@gmail.com>
 * @copyright Copyright © 2015, Nicolas Giraud
 */
class Tile4 extends Tile {
    public $aPossibilities = [
        TOP => [NONE => LEFT, LEFT => RIGHT, RIGHT => RIGHT, HALF => LEFT],
        LEFT => [NONE => null, LEFT => BOTTOM, RIGHT => BOTTOM, HALF => null],
        RIGHT => [NONE => BOTTOM, LEFT => null, RIGHT => null, HALF => BOTTOM],
    ];

    /**
     * @param $dir
     * @return \Indiana_Level3\Tile5
     */
    public function turn($dir) {
        return ($dir === LEFT) ? (new Tile5) : (new Tile5);
    }
}

/**
 * Class Tile5
 *
 * @package   Indiana_Level3
 * @author Nicolas (niconoe) Giraud <nicolas.giraud.dev@gmail.com>
 * @copyright Copyright © 2015, Nicolas Giraud
 */
class Tile5 extends Tile {
    public $aPossibilities = [
        TOP => [NONE => RIGHT, LEFT => LEFT, RIGHT => LEFT, HALF => RIGHT],
        LEFT => [NONE => BOTTOM, LEFT => null, RIGHT => null, HALF => BOTTOM],
        RIGHT => [NONE => null, LEFT => BOTTOM, RIGHT => BOTTOM, HALF => null],
    ];

    /**
     * @param $dir
     * @return \Indiana_Level3\Tile4
     */
    public function turn($dir) {
        return ($dir === LEFT) ? (new Tile4) : (new Tile4);
    }
}

/**
 * Class Tile6
 *
 * @package   Indiana_Level3
 * @author Nicolas (niconoe) Giraud <nicolas.giraud.dev@gmail.com>
 * @copyright Copyright © 2015, Nicolas Giraud
 */
class Tile6 extends Tile {
    public $aPossibilities = [
        TOP => [NONE => null, LEFT => BOTTOM, RIGHT => BOTTOM, HALF => null],
        LEFT => [NONE => RIGHT, LEFT => BOTTOM, RIGHT => BOTTOM, HALF => BOTTOM],
        RIGHT => [NONE => LEFT, LEFT => null, RIGHT => BOTTOM, HALF => BOTTOM],
    ];

    /**
     * @param $dir
     * @return \Indiana_Level3\Tile7|\Indiana_Level3\Tile9
     */
    public function turn($dir) {
        return ($dir === LEFT) ? (new Tile9) : (new Tile7);
    }
}

/**
 * Class Tile7
 *
 * @package   Indiana_Level3
 * @author Nicolas (niconoe) Giraud <nicolas.giraud.dev@gmail.com>
 * @copyright Copyright © 2015, Nicolas Giraud
 */
class Tile7 extends Tile {
    public $aPossibilities = [
        TOP => [NONE => BOTTOM, LEFT => null, RIGHT => null, HALF => BOTTOM],
        LEFT => [NONE => null, LEFT => RIGHT, RIGHT => BOTTOM, HALF => BOTTOM],
        RIGHT => [NONE => BOTTOM, LEFT => LEFT, RIGHT => BOTTOM, HALF => null],
    ];

    /**
     * @param $dir
     * @return \Indiana_Level3\Tile6|\Indiana_Level3\Tile8
     */
    public function turn($dir) {
        return ($dir === LEFT) ? (new Tile6) : (new Tile8);
    }
}

/**
 * Class Tile8
 *
 * @package   Indiana_Level3
 * @author Nicolas (niconoe) Giraud <nicolas.giraud.dev@gmail.com>
 * @copyright Copyright © 2015, Nicolas Giraud
 */
class Tile8 extends Tile {
    public $aPossibilities = [
        TOP => [NONE => null, LEFT => BOTTOM, RIGHT => BOTTOM, HALF => null],
        LEFT => [NONE => BOTTOM, LEFT => null, RIGHT => BOTTOM, HALF => RIGHT],
        RIGHT => [NONE => BOTTOM, LEFT => BOTTOM, RIGHT => null, HALF => LEFT],
    ];

    /**
     * @param $dir
     * @return \Indiana_Level3\Tile7|\Indiana_Level3\Tile9
     */
    public function turn($dir) {
        return ($dir === LEFT) ? (new Tile7) : (new Tile9);
    }
}

/**
 * Class Tile9
 *
 * @package   Indiana_Level3
 * @author Nicolas (niconoe) Giraud <nicolas.giraud.dev@gmail.com>
 * @copyright Copyright © 2015, Nicolas Giraud
 */
class Tile9 extends Tile {
    public $aPossibilities = [
        TOP => [NONE => BOTTOM, LEFT => null, RIGHT => null, HALF => BOTTOM],
        LEFT => [NONE => BOTTOM, LEFT => RIGHT, RIGHT => RIGHT, HALF => null],
        RIGHT => [NONE => null, LEFT => BOTTOM, RIGHT => LEFT, HALF => BOTTOM],
    ];

    /**
     * @param $dir
     * @return \Indiana_Level3\Tile6|\Indiana_Level3\Tile8
     */
    public function turn($dir) {
        return ($dir === LEFT) ? (new Tile8) : (new Tile6);
    }
}

/**
 * Class Tile10
 *
 * @package   Indiana_Level3
 * @author Nicolas (niconoe) Giraud <nicolas.giraud.dev@gmail.com>
 * @copyright Copyright © 2015, Nicolas Giraud
 */
class Tile10 extends Tile {
    public $aPossibilities = [
        TOP => [NONE => LEFT, LEFT => null, RIGHT => RIGHT, HALF => null],
        LEFT => [NONE => null, LEFT => BOTTOM, RIGHT => null, HALF => null],
        RIGHT => [NONE => null, LEFT => null, RIGHT => null, HALF => BOTTOM],
    ];

    /**
     * @param $dir
     * @return \Indiana_Level3\Tile11|\Indiana_Level3\Tile13
     */
    public function turn($dir) {
        return ($dir === LEFT) ? (new Tile13) : (new Tile11);
    }
}

/**
 * Class Tile11
 *
 * @package   Indiana_Level3
 * @author Nicolas (niconoe) Giraud <nicolas.giraud.dev@gmail.com>
 * @copyright Copyright © 2015, Nicolas Giraud
 */
class Tile11 extends Tile {
    public $aPossibilities = [
        TOP => [NONE => RIGHT, LEFT => LEFT, RIGHT => null, HALF => null],
        LEFT => [NONE => null, LEFT => null, RIGHT => null, HALF => BOTTOM],
        RIGHT => [NONE => null, LEFT => null, RIGHT => BOTTOM, HALF => null],
    ];

    /**
     * @param $dir
     * @return \Indiana_Level3\Tile10|\Indiana_Level3\Tile12
     */
    public function turn($dir) {
        return ($dir === LEFT) ? (new Tile10) : (new Tile12);
    }
}

/**
 * Class Tile12
 *
 * @package   Indiana_Level3
 * @author Nicolas (niconoe) Giraud <nicolas.giraud.dev@gmail.com>
 * @copyright Copyright © 2015, Nicolas Giraud
 */
class Tile12 extends Tile {
    public $aPossibilities = [
        TOP => [NONE => null, LEFT => RIGHT, RIGHT => null, HALF => LEFT],
        LEFT => [NONE => null, LEFT => null, RIGHT => BOTTOM, HALF => null],
        RIGHT => [NONE => BOTTOM, LEFT => null, RIGHT => null, HALF => null],
    ];

    /**
     * @param $dir
     * @return \Indiana_Level3\Tile11|\Indiana_Level3\Tile13
     */
    public function turn($dir) {
        return ($dir === LEFT) ? (new Tile11) : (new Tile13);
    }
}

/**
 * Class Tile13
 *
 * @package   Indiana_Level3
 * @author Nicolas (niconoe) Giraud <nicolas.giraud.dev@gmail.com>
 * @copyright Copyright © 2015, Nicolas Giraud
 */
class Tile13 extends Tile {
    public $aPossibilities = [
        TOP => [NONE => null, LEFT => null, RIGHT => LEFT, HALF => RIGHT],
        LEFT => [NONE => BOTTOM, LEFT => null, RIGHT => null, HALF => BOTTOM],
        RIGHT => [NONE => null, LEFT => BOTTOM, RIGHT => null, HALF => null],
    ];

    /**
     * @param $dir
     * @return \Indiana_Level3\Tile10|\Indiana_Level3\Tile12
     */
    public function turn($dir) {
        return ($dir === LEFT) ? (new Tile12) : (new Tile10);
    }
}

/**
 * Class Indiana
 *
 * @package   Indiana_Level3
 * @author Nicolas (niconoe) Giraud <nicolas.giraud.dev@gmail.com>
 * @copyright Copyright © 2015, Nicolas Giraud
 */
class Indiana
{
    public $x;
    public $y;
    public $sEnteringFrom;

    public $nextMove;
    public $aHistory;

    /**
     * @return $this
     */
    public function readLine() {
        fscanf(STDIN, "%d %d %s", $this->x, $this->y, $this->sEnteringFrom);
        return $this;
    }

    /**
     * Return the Tile where Indiana is
     * @param \Indiana_Level3\Map $oMap
     * @return \Indiana_Level3\Tile
     */
    public function getTile(Map $oMap)
    {
        return $oMap->getTile($this->x, $this->y);
    }

    /**
     * Return the next Tile where Indiana is going
     * @param \Indiana_Level3\Map $oMap The Map object
     * @param string $sDir The direction of the movement of Indiana
     * @return \Indiana_Level3\Tile The next tile
     */
    public function getNextTile(Map $oMap, $sDir)
    {
        return $oMap->getNextTile($this->x, $this->y, $sDir);
    }

    /**
     * Fake move Indiana to the next tile
     * @param \Indiana_Level3\Tile $oNextTile
     * @param string $sDir
     * @return $this
     */
    public function nextMove(Tile $oNextTile, $sDir)
    {
        list($this->x, $this->y) = $oNextTile->getCoordinate();
        $this->sEnteringFrom = $sDir;
        return $this;
    }
}

/**
 * Class Boulder
 *
 * @package   Indiana_Level3
 * @author Nicolas (niconoe) Giraud <nicolas.giraud.dev@gmail.com>
 * @copyright Copyright © 2015, Nicolas Giraud
 */
class Boulder
{
    public $x;
    public $y;
    public $sPos;

    /**
     * @return \Indiana_Level3\Boulder
     */
    public function __construct() {
        fscanf(STDIN, "%d %d %s", $this->x, $this->y, $this->sPos);
        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [$this->x, $this->y, $this->sPos];
    }
}


/**
 * Class Main
 *
 * @package   Indiana_Level3
 * @author Nicolas (niconoe) Giraud <nicolas.giraud.dev@gmail.com>
 * @copyright Copyright © 2015, Nicolas Giraud
 */
class Main
{
    /**
     * @var Map $oMap
     */
    public static $oMap;

    /**
     * @var array $aCommands
     */
    public static $aCommands = [];

    /**
     * @var int $iStepAhead
     */
    public static $iStepAhead = 0;

    /**
     * @var bool|array If bool, previous rotation wasn't a half rotation. If array, contains the tile to rotate and the
     *                 rotation
     */
    public static $aWasHalf = false;

    public static function run()
    {
        // Main process
        self::$oMap = new Map();

        // game loop
        while (1) {

            $oIndiana = new Indiana();
            $oIndiana->readLine();
            self::$oMap->setIndiana($oIndiana);
            self::$oMap->setBoulders();

            if (self::$aWasHalf) {
                list($x, $y, $sRot) = self::$aWasHalf;
                $oTile = self::$oMap->getTile($x, $y);
                self::echoRotation($oTile, $sRot);
                self::$aWasHalf = false;
                continue;
            }

            $oIndianaTile = $oIndiana->getTile(self::$oMap);
            //If Indiana's tile is on the Exit, just wait and problem's solved
            if ($oIndianaTile->isExit(self::$oMap)) {
                self::echoWait();
                break; //We can break the infinite while loop: it's finished!
            }

            //Indiana tile is not able to rotate because Indiana is into.
            //We need to follow the direction
            $bIsFinished = false;
            do {
                $sDir = $oIndianaTile->findDirection($oIndiana->sEnteringFrom);
                $oNextTile = $oIndiana->getNextTile(self::$oMap, $sDir);
                if ($oNextTile->isExit(self::$oMap)) {
                    $bIsFinished = true;
                    self::echoWait();
                    break;
                }

                //If the next tile can't be rotated, prepare to wait and add one step ahead
                if (!$oNextTile->bRot) {
                    if (!self::manageBoulders()) {
                        self::addWait();
                        $sDir = Map::reverse($sDir); //Reverse because we enter in a new Tile
                        $oIndiana->nextMove($oNextTile, $sDir);
                        $oIndianaTile = $oNextTile;
                        continue;
                    }
                    break;
                }
                $sNextEnteringFrom = Map::reverse($sDir);
                $aWays = $oNextTile->getPossibilities($sNextEnteringFrom);
                //No need for specific long analysis if all possibles ways are the same.
                if (count(array_unique($aWays)) === 1) {
                    $sRot = null;
                    foreach ($aWays as $sRot => $sWay) {
                        break;
                    }
                    //If we don't need to rotate the tile, prepare to wait and add one step ahead
                    if ($sRot === NONE) {
                        if (!self::manageBoulders()) {
                            self::addWait();
                            $sDir = Map::reverse($sDir); //Reverse because we enter in a new Tile
                            $oIndiana->nextMove($oNextTile, $sDir);
                            $oIndianaTile = $oNextTile;
                            continue;
                        }
                        break;
                    } else {
                        self::echoRotation($oNextTile, $sRot);
                        break; //Rotation's done so let's continue.
                    }
                }

                $sWay = null;
                $aPotentialPaths = [];
                foreach ($aWays as $sRot => $sWay) {
                    $aPotentialPaths[$sRot] = self::analyzeWay($oNextTile, $sWay);
                }
                asort($aPotentialPaths);
                $sRot = null;
                foreach ($aPotentialPaths as $sRot => $sWay) {
                    break;
                }
                //If we don't need to rotate the tile, prepare to wait and add one step ahead
                if ($sRot === NONE) {
                    if (!self::manageBoulders()) {
                        self::addWait();
                        $sDir = Map::reverse($sDir); //Reverse because we enter in a new Tile
                        $oIndiana->nextMove($oNextTile, $sDir);
                        $oIndianaTile = $oNextTile;
                        continue;
                    }
                    break;
                } else {
                    self::echoRotation($oNextTile, $sRot);
                    break; //Rotation's done so let's continue.
                }
            } while (true);

            if ($bIsFinished) {
                //Lots of rounds have to be un-shift to echo correctly.
                foreach (self::$aCommands as $command) {
                    if ($command === WAIT) {
                        self::echoWait();
                        continue;
                    }
                    list ($oTile, $sRotation) = $command;
                    self::echoRotation($oTile, $sRotation);
                }
                break; //Break the infinite loop
            }
        }
    }

    /**
     * Echo the tile to rotate ans the sense of rotation
     * @param \Indiana_Level3\Tile $oTile The tile to rotate
     * @param string $sRotation The sense of rotation
     */
    public static function echoRotation(Tile $oTile, $sRotation)
    {
        if ($sRotation === HALF) {
            $sRotation = LEFT;
            self::$aWasHalf = [$oTile->x, $oTile->y, LEFT];
        }
        list($x, $y) = $oTile->getCoordinate();
        echo $x . ' ' . $y . ' ' . $sRotation . "\n";
        //Update the map with the rotation
        self::$oMap->updateTile($oTile, $sRotation);
    }

    public static function echoWait()
    {
        echo WAIT . "\n";
    }

    public static function addWait()
    {
        self::$aCommands[] = WAIT;
        self::$iStepAhead++;
    }

    /**
     * @return bool
     */
    public static function manageBoulders()
    {
        $aBoulders = self::$oMap->getBoulders();
        if (empty($aBoulders)) {
            return false;
        }

        //If nextPos boulder == currPos Indiana, don't care
        //Care about the boulder that will travel on the minimum number (> 0) of tiles until a rotation able tile.
        $minNbTile = PHP_INT_MAX;
        $iIndexBoulderPriority = null;
        $oTileToRotate = null;

        $oIndiana = self::$oMap->oIndiana;

        foreach ($aBoulders as $iIndexBoulder => $oBoulder) {
            $nbTileUntilBlocked = 0;
            list($x, $y, $sEnteringFrom) = $oBoulder->toArray();
            $oBoulderTile = self::$oMap->getTile($x, $y);
            $oNextTile = $oBoulderTile;
            do {
                $sDir = $oNextTile->findDirection($sEnteringFrom);
                $oNextTile = $oNextTile->getNextTile(self::$oMap, $sDir);
                $sEnteringFrom = Map::reverse($sDir);
                $nbTileUntilBlocked++;
            } while(!$oNextTile->bRot || ($oNextTile->x === $oIndiana->x && $oNextTile->y === $oIndiana->y));

            if ($oNextTile->x === $oIndiana->x && $oNextTile->y === $oIndiana->y) {
                continue; //Don't care about boulders that are behind Indiana
            }

            if ($minNbTile > $nbTileUntilBlocked) {
                $minNbTile = $nbTileUntilBlocked;
                $iIndexBoulderPriority = $iIndexBoulder;
                $oTileToRotate = $oNextTile;
            }
        }

        $oCriticalBoulder = $aBoulders[$iIndexBoulderPriority];
        $aBlocked = $oTileToRotate->getPossibilitiesToBlock($oCriticalBoulder->sPos);
        if (count($aBlocked) === 1) {
            $sRot = reset($aBlocked);
            if ($sRot === NONE) {
                //No need to rotate, try to take care of another boulder
                unset(self::$oMap->aBoulders[$iIndexBoulderPriority]);
                return self::manageBoulders();
            }
            self::echoRotation($oTileToRotate, $sRot);
            return true; //Job's done, return
        }
        if (empty($aBlocked)) {
            return false;
        }
        $sRot = reset($aBlocked);
        if ($sRot === NONE) {
            //No need to rotate, try to take care of another boulder
            unset(self::$oMap->aBoulders[$iIndexBoulderPriority]);
            return self::manageBoulders();
        }
        self::echoRotation($oTileToRotate, $sRot);
        return true; //Job's done, return
    }

    /**
     * @param \Indiana_Level3\Tile $oTile
     * @param                      $sWay
     * @return int|mixed
     */
    public static function analyzeWay(Tile $oTile, $sWay)
    {
        $oNextTile = $oTile->getNextTile(self::$oMap, $sWay);
        $oFakeIndiana = new Indiana();
        $oFakeIndiana->x = $oNextTile->x;
        $oFakeIndiana->y = $oNextTile->y;
        $oFakeIndiana->sEnteringFrom = Map::reverse($sWay);

        if ($oNextTile->isExit(self::$oMap)) {
            return 0;
        }

        $aWays = $oNextTile->getPossibilities(Map::reverse($sWay));
        if (empty($aWays)) {
            return PHP_INT_MAX;
        }
        if (count(array_unique($aWays)) === 1) {
            $sRot = $sNewWay = null;
            foreach ($aWays as $sRot => $sNewWay) {
                break;
            }
            if ($sRot === NONE) {
                return 0 + self::analyzeWay($oNextTile, $sNewWay);
            } elseif ($sRot === LEFT || $sRot === RIGHT) {
                return 1 + self::analyzeWay($oNextTile, $sNewWay);
            } else { //HALF
                return 2 + self::analyzeWay($oNextTile, $sNewWay);
            }
        }

        $aPotentialPaths = [];
        foreach ($aWays as $sRot => $sWay) {
            $aPotentialPaths[$sRot] = self::analyzeWay($oNextTile, $sWay);
        }
        asort($aPotentialPaths);
        $nbRotFuture = reset($aPotentialPaths);
        $sRotCurrent = key($aPotentialPaths);
        if ($sRotCurrent === NONE) {
            $nbRotCurrent = 0;
        } elseif ($sRotCurrent === LEFT || $sRotCurrent === RIGHT) {
            $nbRotCurrent = 1;
        } else { //HALF
            $nbRotCurrent = 2;
        }
        return $nbRotFuture + $nbRotCurrent;
    }
}

Main::run();
?>