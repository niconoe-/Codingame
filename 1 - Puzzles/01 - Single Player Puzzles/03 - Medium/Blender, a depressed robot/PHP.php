<?php
define('DEBUG', true);
function debug() {if (!DEBUG) return; foreach (func_get_args() as $sArgDebug) error_log(var_export($sArgDebug, true));}

define('S', 'SOUTH');
define('N', 'NORTH');
define('E', 'EAST');
define('W', 'WEST');

class Histo
{
    public static $aHisto = array();
    
    public static function add($fingerPrint)
    {
        self::$aHisto[$fingerPrint] = true;
    }
    
    public static function exist($fingerPrint)
    {
        return (isset(self::$aHisto[$fingerPrint]));
    }
    
}

class Map
{
    public $aNextMoveBlock = array(W => S, N => E, E => N, S => W);
    public $aTriedMoves = array();
    public $aMap = array();
    public $x = null;
    public $y = null;
    public $previousCase = ' ';
    public $nextMove = S;
    public $bIsBeer = false;
    public $aTele = array();
    public $bIsTeleporting = false;
    
    public $aAllMoves = array();
    
    public $bIsEnd = false;
    public $bIsLoop = false;
    
    public function setPosition($x, $y)
    {
        $this->x = $x;
        $this->y = $y;
        return $this;
    }
    
    public function addTeleporteur($x, $y)
    {
        $this->aTele[] = array($x, $y);
        return $this;
    }
    
    public function teleport()
    {
        $aPos = array($this->x, $this->y);
        if ($this->aTele[0] === $aPos) {
            list($newX, $newY) = $this->aTele[1];
        } else {
            list($newX, $newY) = $this->aTele[0];
        }
        debug('Blender is teleporting from (' . $this->x . ', ' . $this->y . ') to (' . $newX . ', ' . $newY . ').');
        $this->setPosition($newX, $newY);
        $this->bIsTeleporting = false;
        $this->previousCase = 'T';
    }
    
    public function checkHisto()
    {
        $fingerPrintSituation = md5(serialize($this));
        if (Histo::exist($fingerPrintSituation)) {
            $this->bIsLoop = true;
            return false;
        }
        Histo::add($fingerPrintSituation);
        return true;
    }
    
    public function getMapPosition()
    {
        return $this->aMap[$this->y][$this->x];
    }
    
    public function move() 
    {
        $this->show();
        
        if (!$this->checkHisto()) {
            //LOOP detected here
            return true;
        }
        $prevX = $this->x;
        $prevY = $this->y;
        
        do {
            debug('Can Blender go to ' . $this->nextMove . '?');
            $bMoveDone = false;
            $this->_prepareMove();
            $nextCase = $this->getMapPosition();
            $canMove = $this->_canMove($nextCase);
            if ($canMove) {
                debug('At ' . $this->nextMove . ', there is case "' . $nextCase . '". Blender can go.');
                $this->aMap[$prevY][$prevX] = $this->previousCase;
                $this->previousCase = ($nextCase === 'X' ? ' ' : $nextCase);
                $this->aAllMoves[] = $this->nextMove;
                $this->_analyseCase($nextCase);
                if ($this->bIsTeleporting) {
                    $this->teleport();
                }
                $this->aMap[$this->y][$this->x] = '@';
                
                $this->aTriedMoves = array();
                $bMoveDone = true;
            } else {
                debug('At ' . $this->nextMove . ', there is case "' . $nextCase . '". Blender cannot go.');
                //Reset position
                $this->setPosition($prevX, $prevY);
                $this->aTriedMoves[] = $this->nextMove;
                $aLeftMoves = array_diff($this->aNextMoveBlock, $this->aTriedMoves);
                //If no more move possible, Blender is blocked
                if (empty($aLeftMoves)) {
                    $bMoveDone = true;
                    $this->bIsLoop = true;
                    return true;
                }
                //Otherwise, go to next move
                $this->nextMove = reset($aLeftMoves);
                debug('Try : ' . $this->nextMove);
                return $this->move();
            }
        } while(!$bMoveDone);
        
        return $this->bIsEnd;
    }
    
    protected function _prepareMove()
    {
        if ($this->nextMove === S) {
            $this->y++;
        } elseif ($this->nextMove === E) {
            $this->x++;
        } elseif ($this->nextMove === N) {
            $this->y--;
        } else {
            $this->x--;
        }
        
        return $this;
    }
    
    /**
     * return true if Blender can move. false otherwise
     */
    protected function _canMove($case)
    {
        if ($case === '#' || (!$this->bIsBeer && $case === 'X')) {
            return false;
        }
        return true;
    }
    
    protected function _analyseCase($case)
    {
        if ($case === '$') {
            $this->bIsEnd = true;
            return;
        }
        
        if ($case === 'S' || $case === 'E' || $case === 'N' || $case === 'W') {
            $this->nextMove = constant($case);
            return;
        }
        
        if ($case === 'B') {
            $this->bIsBeer = !$this->bIsBeer;
            return;
        }
        
        if ($case === 'I') {
            $this->aNextMoveBlock = array_flip($this->aNextMoveBlock);
            return;
        }
        
        if ($case === 'T') {
            $this->bIsTeleporting = true;
            return;
        }
    }
    
    
    public function __sleep()
    {
        $aUnwantedParams = array(
            'bIsLoop',
            'bIsEnd',
            'bIsTeleporting',
            'aAllMoves',
            'aTele',
        );
        return array_diff(array_keys(get_object_vars($this)), $aUnwantedParams);
    }
    
    
    
    
    
    public function show()
    {
        if (!DEBUG) {
            return $this;
        }
        $sMap = "Current Map:\n";
        for ($i=0, $L=count($this->aMap); $i<$L; ++$i) {
            for ($j=0, $C=count($this->aMap[$i]); $j<$C; ++$j) {
                $sMap .= $this->aMap[$i][$j];
            }
            $sMap .= "\n";
        }
        debug($sMap);
        return $this;
    }
    
    public function displayOut()
    {
        foreach ($this->aAllMoves as $sMove) {
            echo $sMove . "\n";
        }
    }
}


$oMap = new Map();
fscanf(STDIN, "%d %d", $L, $C);
for ($j = 0; $j < $L; ++$j) {
    $row = stream_get_line(STDIN, $C+1, "\n");
    $oMap->aMap[$j] = str_split(trim($row));
    if (false !== ($i = strpos($row, '@'))) {
        $oMap->setPosition($i, $j);
    }
    if (false !== ($i = strpos($row, 'T'))) {
        $oMap->addTeleporteur($i, $j);
    }
}

do {
    $isStop = $oMap->move();
}
while(!$isStop);
if ($oMap->bIsLoop) {
    echo "LOOP\n";
} else {
    $oMap->displayOut();
}
?>