<?php
define('DEBUG', true);
function debug() {if (!DEBUG) return; foreach (func_get_args() as $sArgDebug) error_log(var_export($sArgDebug, true));}
function e($a, $b){echo "$a $b\n";}
function wait(){echo "WAIT\n";}

define('ROUND_EXPL', 3);

class Firewall {
    
    public $w;
    public $h;
    
    public $map;
    
    public function __construct()
    {
        fscanf(STDIN, "%d %d", $this->w, $this->h);
        for ($i = 0; $i < $this->h; ++$i) {
            fscanf(STDIN, "%s", $mapRow);
            $this->map[$i] = str_split($mapRow);
        }
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
                $aMapLine[] = $this->map[$y][$x];
            }
        }
        $aNbCountValues = array_count_values($aMapLine);
        return ($aNbCountValues['@']);
    }
    
    private function _calcNbNodesDestroyed($x, $y)
    {
        if ($this->map[$y][$x] === '@' || $this->map[$y][$x] === '#') {
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
            if (!isset($this->map[$iy][$ix]) || $this->map[$iy][$ix] === '#') {
                return $nbCurr;
            }
            if ($this->map[$iy][$ix] === '@') {
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
        if (!isset($this->map[$y][$x])) {
            return;
        }
        if ($this->map[$y][$x] !== '#') {
            $this->map[$y][$x] = ROUND_EXPL;
        }
    }
    
    public function nextRound()
    {
        for ($y=0; $y<$this->h; ++$y) {
            for ($x=0; $x<$this->w; ++$x) {
                if ($this->map[$y][$x] === 3) {
                    $this->map[$y][$x] = 2;
                } elseif ($this->map[$y][$x] === 2) {
                    $this->map[$y][$x] = 1;
                } elseif ($this->map[$y][$x] === 1) {
                    $this->map[$y][$x] = '.';
                }
            }
        }
    }
    
    public function placeBomb($x, $y, $fake = false) 
    {
        if ($this->map[$y][$x] === '.') {
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

class Info {
    
    public $rounds;
    public $bombs;
    
    public function readLine()
    {
        fscanf(STDIN, "%d %d", $this->rounds, $this->bombs);
    }
    
    public function dropBomb($i, $oMap, $fake = false) {
        $y = floor($i / $oMap->w);
        $x = $i % $oMap->w;
        
        return $oMap->placeBomb($x, $y, $fake);
    }
    
    public function testDropBomb($oMap, $aCoords) {
        
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
    
    $oMap->nextRound();
    $oInfo->readLine();
    
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
        
        $aMapSaved = $oMap->map;
        if ($oInfo->testDropBomb($oMap, $aCoords)) {
            $oMap->map = $aMapSaved;
            $oInfo->dropBomb($index, $oMap);
            break;
        } else {
            $oMap->map = $aMapSaved;
            $aCoords = array_slice($aCoords, 1, null, true);
        }
    } while (true);
}
?>