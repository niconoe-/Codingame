<?php
namespace VoxCodei_Redux;

function debug() {foreach (func_get_args() as $sArgDebug) error_log(var_export($sArgDebug, true));}
function e($a, $b){echo "$a $b\n";}
function wait($sMsg = null){echo "WAIT\n"; !$sMsg ?: debug($sMsg);}

define('ROUND_EXPL', 3);

class Map extends \ArrayObject
{
    public function __toString()
    {
        $sDebug = '';
        for ($i=0, $nb=$this->count(); $i<$nb; ++$i) {
            $sDebug .= implode('', $this[$i]) . PHP_EOL;
        }
        return $sDebug;
    }

    public function debug()
    {
        error_log($this->__toString());
    }
}

class Firewall
{

    public $w;
    public $h;

    /** @var Map */
    public $aPreviousMap;

    /** @var Map */
    public $aCurrentMap;

    /** @var Map[] */
    public $aNextMaps;

    /** @var array */
    public $aNodeMoves;

    public function __construct()
    {
        fscanf(STDIN, "%d %d", $this->w, $this->h);
        $this->aPreviousMap = null;
        $this->aCurrentMap = null;
        $this->aNextMaps = [];
    }

    public function drawMap()
    {
        $this->aCurrentMap = new Map();
        for ($i = 0; $i < $this->h; ++$i) {
            fscanf(STDIN, "%s", $mapRow);
            $this->aCurrentMap[$i] = str_split($mapRow);
        }
        return $this;
    }

    public function drawNextMap($i)
    {
        $this->aNextMaps[$i] = 'TODO';
    }

    public function compareMaps(Map $oMapOrigin, Map $oMapTarget)
    {
        $aCompNode = [];
        $i = 0;
        for ($y=0; $y<$this->h; ++$y) {
            for ($x=0; $x<$this->w; ++$x) {
                if ($oMapOrigin[$y][$x] !== '@') {
                    continue;
                }
                //There was a node at this origin. Where is it now?
                if ($oMapTarget[$y][$x] === '@') { //not move?
                    $aCompNode[$i] = [
                        'startCoordinates' => [$x, $y],
                        'currentCoordinates' => [$x, $y],
                        'startMove' => 'NONE'
                    ];
                } elseif (isset($oMapTarget[$y-1][$x]) && $oMapTarget[$y-1][$x] === '@') { //move up?
                    $aCompNode[$i] = [
                        'startCoordinates' => [$x, $y],
                        'currentCoordinates' => [$x, $y-1],
                        'startMove' => 'UP'
                    ];
                } elseif (isset($oMapTarget[$y][$x+1]) && $oMapTarget[$y][$x+1] === '@') { //move right?
                    $aCompNode[$i] = [
                        'startCoordinates' => [$x, $y],
                        'currentCoordinates' => [$x+1, $y],
                        'startMove' => 'RIGHT'
                    ];
                } elseif (isset($oMapTarget[$y][$x-1]) && $oMapTarget[$y][$x-1] === '@') { //move left?
                    $aCompNode[$i] = [
                        'startCoordinates' => [$x, $y],
                        'currentCoordinates' => [$x-1, $y],
                        'startMove' => 'LEFT'
                    ];
                } elseif (isset($oMapTarget[$y+1][$x]) && $oMapTarget[$y+1][$x] === '@') { //move down?
                    $aCompNode[$i] = [
                        'startCoordinates' => [$x, $y],
                        'currentCoordinates' => [$x, $y+1],
                        'startMove' => 'DOWN'
                    ];
                }
                $i++;
            }
        }

        return $aCompNode;
    }

    public function findBestCoord()
    {
        $aCoords = [];
        for ($y=0; $y<$this->h; ++$y) {
            for ($x=0; $x<$this->w; ++$x) {
                $i = ($this->w * $y + $x);
                $aCoords[$i] = $this->_calcNbNodesDestroyed($x, $y);
            }
        }
        return $aCoords;
    }

    public function nbNodes()
    {
        $aMapLine = [];
        for ($y=0; $y<$this->h; ++$y) {
            for ($x=0; $x<$this->w; ++$x) {
                $aMapLine[] = $this->aCurrentMap[$y][$x];
            }
        }
        $aNbCountValues = array_count_values($aMapLine);
        return $aNbCountValues['@'];
    }

    private function _calcNbNodesDestroyed($x, $y)
    {
        if ($this->aCurrentMap[$y][$x] === '@' || $this->aCurrentMap[$y][$x] === '#') {
            return 0;
        }

        $nbNodes = 0;
        foreach (['UP', 'RIGHT', 'LEFT', 'DOWN'] as $sDirection) {
            $nbNodes += $this->_calcNbNodesDestroyedDirection($x, $y, $sDirection);
        }
        return $nbNodes;
    }

    private function _calcNbNodesDestroyedDirection($x, $y, $sDir)
    {
        $xDir = 0;
        $yDir = 0;
        ($sDir === 'UP') ? ($xDir = -1) : null;
        ($sDir === 'DOWN') ? ($xDir = 1) : null;
        ($sDir === 'LEFT') ? ($yDir = -1) : null;
        ($sDir === 'RIGHT') ? ($yDir = 1) : null;

        $nbCurr = 0;
        for ($i=1; $i<=3; ++$i) {
            $ix = $x+($xDir*$i);
            $iy = $y+($yDir*$i);
            if (!isset($this->aCurrentMap[$iy][$ix]) || $this->aCurrentMap[$iy][$ix] === '#') {
                return $nbCurr;
            }
            if ($this->aCurrentMap[$iy][$ix] === '@') {
                ++$nbCurr;
            }
        }
        return $nbCurr;
    }

    public function updateMap($x, $y)
    {
        $this->_updateCeil($x, $y);
        $this->_updateCeil($x, $y-1);
        $this->_updateCeil($x, $y-2);
        $this->_updateCeil($x, $y-3);
        $this->_updateCeil($x, $y+1);
        $this->_updateCeil($x, $y+2);
        $this->_updateCeil($x, $y+3);
        $this->_updateCeil($x-1, $y);
        $this->_updateCeil($x-2, $y);
        $this->_updateCeil($x-3, $y);
        $this->_updateCeil($x+1, $y);
        $this->_updateCeil($x+2, $y);
        $this->_updateCeil($x+3, $y);
    }

    private function _updateCeil($x, $y)
    {
        if (!isset($this->aCurrentMap[$y][$x])) {
            return;
        }
        if ($this->aCurrentMap[$y][$x] !== '#') {
            $this->aCurrentMap[$y][$x] = ROUND_EXPL;
        }
    }

    public function nextRound()
    {
        for ($y=0; $y<$this->h; ++$y) {
            for ($x=0; $x<$this->w; ++$x) {
                if ($this->aCurrentMap[$y][$x] === 3) {
                    $this->aCurrentMap[$y][$x] = 2;
                } elseif ($this->aCurrentMap[$y][$x] === 2) {
                    $this->aCurrentMap[$y][$x] = 1;
                } elseif ($this->aCurrentMap[$y][$x] === 1) {
                    $this->aCurrentMap[$y][$x] = '.';
                }
            }
        }
    }

    /**
     * @param int $x
     * @param int $y
     * @param bool $fake
     * @return bool
     */
    public function placeBomb($x, $y, $fake = false)
    {
        if ($this->aCurrentMap[$y][$x] === '.') {
            if (!$fake) {
                e($x, $y);
            }
            $this->updateMap($x, $y);
            return true;
        }
        wait();
        return false;
    }
}

class Info
{

    public $rounds;
    public $bombs;

    public function readLine()
    {
        fscanf(STDIN, "%d %d", $this->rounds, $this->bombs);
    }

    /**
     * @param int $i
     * @param Firewall $oMap
     * @param bool $fake
     * @return bool
     */
    public function dropBomb($i, $oMap, $fake = false)
    {
        $y = floor($i / $oMap->w);
        $x = $i % $oMap->w;

        return $oMap->placeBomb($x, $y, $fake);
    }

    /**
     * @param Firewall $oMap
     * @param array $aCoords
     * @return bool
     */
    public function testDropBomb($oMap, $aCoords)
    {

        $nbNodesLeft = $oMap->nbNodes() - reset($aCoords);
        $nbBombs = $this->bombs - 1;

        $this->dropBomb(key($aCoords), $oMap, true);
        $aCoords = $oMap->findBestCoord();
        arsort($aCoords);
        $aBestCoords = array_slice($aCoords, 0, $nbBombs);

        return (array_sum($aBestCoords) >= $nbNodesLeft);
    }


}

$oMap = new Firewall();
$oInfo = new Info();

while (1) {

    $oMap->aPreviousMap = $oMap->aCurrentMap;

    $oInfo->readLine();
    $oMap->drawMap();

    //If previous map is null, it's the first step so we can't know the direction of moving nodes. Wait to find them.
    if (null === $oMap->aPreviousMap) {
        wait('First step, so analyse next step.');
        continue;
    }

    $oMap->aNodeMoves = $oMap->compareMaps($oMap->aPreviousMap, $oMap->aCurrentMap);
    debug($oMap->aNodeMoves);
    $oMap->aNextMaps[0] = $oMap->aCurrentMap;

    for ($i = 1; $i<=ROUND_EXPL; ++$i) {
        $oMap->drawNextMap($i);
    }

    $oMap->nextRound();

    if ($oInfo->bombs === 0) {
        wait();
        continue;
    }

    $aCoords = $oMap->findBestCoord();
    arsort($aCoords);

    do {
        $nbExplode = reset($aCoords);
        $index = key($aCoords);
        if ($nbExplode === 0) {
            wait();
            break;
        }

        $aMapSaved = $oMap->aCurrentMap;
        if ($oInfo->testDropBomb($oMap, $aCoords)) {
            $oMap->aCurrentMap = $aMapSaved;
            $oInfo->dropBomb($index, $oMap);
            break;
        } else {
            $oMap->aCurrentMap = $aMapSaved;
            $aCoords = array_slice($aCoords, 1, null, true);
        }
    } while (true);
}