<?php
namespace Le_Labyrinthe;

define('DEBUG', true);
function debug() {if (!DEBUG) return; foreach (func_get_args() as $sArgDebug) error_log(var_export($sArgDebug, true));}

define('UP', "UP\n");
define('DOWN', "DOWN\n");
define('LEFT', "LEFT\n");
define('RIGHT', "RIGHT\n");

define('NB_ROUND_START', 1200);

/**
 * Class Labyrinth
 *
 * @package Le_Labyrinthe
 * @author Nicolas (niconoe) Giraud <nicolas.giraud.dev@gmail.com>
 * @copyright Copyright © 2015, Nicolas Giraud
 */
class Labyrinth
{
    public $aMap;

    public $iRows;
    public $iCols;

    public $iNbRoundAlarm;
    public $iNbRoundLeft;

    public $oKirk;

    public function __construct()
    {
        fscanf(STDIN, "%d %d %d", $this->iRows, $this->iCols, $this->iNbRoundAlarm);
        $this->iNbRoundLeft = NB_ROUND_START;
    }

    public function mapGet($x, $y)
    {
        if (isset($this->aMap[$y][$x])) {
            return $this->aMap[$y][$x];
        }
        return false;
    }

    public function initMap()
    {
        $aPreviousMap = $this->aMap;
        $aNewMap = [];
        for ($i = 0; $i < $this->iRows; ++$i) {
            fscanf(STDIN, "%s", $aNewMap[]);
        }

        if (!empty($aPreviousMap)) {
            for ($i = 0; $i < $this->iRows; ++$i) {
                for ($j = 0; $j < $this->iCols; ++$j) {
                    if ($aPreviousMap[$i][$j] === 'V') {
                        $aNewMap[$i][$j] = 'V';
                    }
                    if ($aPreviousMap[$i][$j] === 'X') {
                        $aNewMap[$i][$j] = 'X';
                    }
                }
            }
        }

        $this->aMap = $aNewMap;
        return $this;
    }

    public function placeKirk(Kirk $oKirk)
    {
        $this->oKirk = $oKirk;
        return $this;
    }

    public function placePreviousVisited(Kirk $oKirk)
    {
        list ($xPrev, $yPrev) = $oKirk->getPreviousCoordinates();
        if ('.' === $this->mapGet($xPrev, $yPrev)) {
            $this->aMap[$yPrev][$xPrev] = 'V';
        }
    }

    public function removePreviousVisited(Kirk $oKirk)
    {
        list ($xPrev, $yPrev) = $oKirk->getPreviousCoordinates();
        if ('.' === $this->mapGet($xPrev, $yPrev) || 'V' === $this->mapGet($xPrev, $yPrev)) {
            $this->aMap[$yPrev][$xPrev] = 'X';
        }
    }

    public function getPossibleMovementForKirk()
    {
        $aPossibleMvt = [];
        $x = $this->oKirk->x;
        $y = $this->oKirk->y;

        if ($this->mapGet($x-1, $y) !== '#') {
            $aPossibleMvt[] = LEFT;
        }
        if ($this->mapGet($x+1, $y) !== '#') {
            $aPossibleMvt[] = RIGHT;
        }
        if ($this->mapGet($x, $y-1) !== '#') {
            $aPossibleMvt[] = UP;
        }
        if ($this->mapGet($x, $y+1) !== '#') {
            $aPossibleMvt[] = DOWN;
        }

        return $aPossibleMvt;
    }

    public function isKirkAtCommand()
    {
        return ($this->mapGet($this->oKirk->x, $this->oKirk->y) === 'C');
    }


    public function nextAlreadyVisited(Kirk $oKirk, $sMvt)
    {
        list($x, $y) = $oKirk->getNextCoordinates($sMvt);
        return ('V' === $this->mapGet($x, $y) || 'X' === $this->mapGet($x, $y));
    }

    public function getOptimizedDirection($aPossibleDirections)
    {
        $iMaxUnknown = 0;
        $sBestDir = null;
        foreach ($aPossibleDirections as $sDirAttempt) {
            if ($sDirAttempt === LEFT) {
                $xDir = -1;
                $yDir = 0;
            } elseif ($sDirAttempt === RIGHT) {
                $xDir = 1;
                $yDir = 0;
            } elseif ($sDirAttempt === UP) {
                $xDir = 0;
                $yDir = -1;
            } else {
                $xDir = 0;
                $yDir = 1;
            }

            $i = 0;
            $iNbUnknown = 0;
            while (false !== ($sPos = $this->mapGet($this->oKirk->x + $i*$xDir, $this->oKirk->y + $i*$yDir))) {
                ++$i;
                if ($sPos === '?') {
                    ++$iNbUnknown;
                }
            }

            if ($iNbUnknown > $iMaxUnknown) {
                $iMaxUnknown = $iNbUnknown;
                $sBestDir = $sDirAttempt;
            }
        }

        return $sBestDir;
    }

    public function findPreviousPath()
    {
        $aPrevious = [
            LEFT => ($this->mapGet($this->oKirk->x - 1, $this->oKirk->y) === 'V'),
            RIGHT => ($this->mapGet($this->oKirk->x + 1, $this->oKirk->y) === 'V'),
            DOWN => ($this->mapGet($this->oKirk->x, $this->oKirk->y + 1) === 'V'),
            UP => ($this->mapGet($this->oKirk->x, $this->oKirk->y - 1) === 'V'),
        ];

        $aPrevious = array_filter($aPrevious);

        //If empty, We're next to the start point, so just find the good direction of it
        if (empty($aPrevious)) {
            $aStartPoint = [
                LEFT => ($this->mapGet($this->oKirk->x - 1, $this->oKirk->y) === 'T'),
                RIGHT => ($this->mapGet($this->oKirk->x + 1, $this->oKirk->y) === 'T'),
                DOWN => ($this->mapGet($this->oKirk->x, $this->oKirk->y + 1) === 'T'),
                UP => ($this->mapGet($this->oKirk->x, $this->oKirk->y - 1) === 'T'),
            ];

            $aStartPoint = array_filter($aStartPoint);
            reset($aStartPoint);
            return key($aStartPoint);
        }

        reset($aPrevious);
        return key($aPrevious);
    }

    public function isCommandCenterFound()
    {
        return (false !== strpos(implode('', $this->aMap), 'C'));
    }

    public function findShortestReturnPath()
    {
        $sMapInline = implode('', $this->aMap);
        $iPosCommand = strpos($sMapInline, 'C');
        $iPosStart = strpos($sMapInline, 'T');
        return $this->_doBFS($iPosCommand, $iPosStart, $this->iNbRoundAlarm);
    }

    public function findShortestKirkPath()
    {
        $sMapInline = implode('', $this->aMap);
        $iPosCommand = strpos($sMapInline, 'C');
        $iPosKirk = $this->oKirk->x + $this->oKirk->y *$this->iCols;
        $aKirkPath = $this->_doBFS($iPosKirk, $iPosCommand);
        $aKirkPath[] = $iPosCommand; //Add Center command position at the end
        return $aKirkPath;
    }

    private function _doBFS($iPosStart, $iPosEnd, $limit = null)
    {
        if ($limit === null) {
            $limit = NB_ROUND_START;
        }

        //Staring from $iPosStart and try to reach $iPosEnd in less than $limit rounds.
        $nbRound = 0;
        $aStack = [[$iPosStart]];
        $aVisited = [];
        $bFound = false;
        while (!empty($aStack) && $nbRound <= $limit && !$bFound) {
            $aCurrentStack = array_shift($aStack);
            $aNewStack = [];
            foreach ($aCurrentStack as $iPos) {
                if (isset($aVisited[$iPos])) {
                    continue;
                }
                $aVisited[$iPos] = $nbRound;
                list($x, $y) = [$iPos % $this->iCols, floor($iPos / $this->iCols)];

                !($this->_checkNextBFS($x, $y-1, $iPosEnd, $nbRound, $aNewStack, $aVisited)) ?: $bFound = true;
                !($this->_checkNextBFS($x-1, $y, $iPosEnd, $nbRound, $aNewStack, $aVisited)) ?: $bFound = true;
                !($this->_checkNextBFS($x, $y+1, $iPosEnd, $nbRound, $aNewStack, $aVisited)) ?: $bFound = true;
                !($this->_checkNextBFS($x+1, $y, $iPosEnd, $nbRound, $aNewStack, $aVisited)) ?: $bFound = true;

                if ($bFound) {
                    break;
                }
            }
            if (!empty($aNewStack)) {
                $aStack[] = array_unique($aNewStack);
            }
            ++$nbRound;
        }

        //If path not found, continue to explore
        if (!isset($aVisited[$iPosEnd])) {
            return false;
        }

        $nbRoundToStart = $aVisited[$iPosEnd];
        //If the path isn't short as necessary, continue to explore.
        if ($limit < $nbRoundToStart) {
            return false;
        }

        //The path is correct, return all coordinates
        krsort($aVisited);
        $aPath = [];
        $aPath[] = $iPosEnd;
        $iPos = $iPosEnd;
        for ($i = $nbRoundToStart-1; $i > 0; --$i) {
            $aPossibleVisitedPath = array_keys($aVisited, $i, true);
            foreach ($aPossibleVisitedPath as $iPossiblePos) {
                if ($iPos+1 === $iPossiblePos) {
                    $iPos += 1;
                    break;
                }
                if ($iPos-1 === $iPossiblePos) {
                    $iPos -= 1;
                    break;
                }
                if ($iPos+$this->iCols === $iPossiblePos) {
                    $iPos += $this->iCols;
                    break;
                }
                if ($iPos-$this->iCols === $iPossiblePos) {
                    $iPos -= $this->iCols;
                    break;
                }
            }
            $aPath[] = $iPos;
        }
        return array_reverse($aPath);
    }

    private function _checkNextBFS($x, $y, $iPosEnd, $nbRound, &$aNewStack, &$aVisited)
    {
        $iPos = $x + $y * $this->iCols;
        if (isset($aVisited[$iPos])) {
            return false;
        }

        $sPos = $this->mapGet($x, $y);
        ($sPos === '#' || $sPos === '?') ?: $aNewStack[] = $iPos;

        if (intval($iPos) === intval($iPosEnd)) {
            $aVisited[$iPos] = ($nbRound+1);
            return true;
        }

        return false;
    }

    public function moveFollowingPath(array &$aPath)
    {
        //Use the $aFinalePath to retrieve the smallest path to the start.
        $iNextPos = array_shift($aPath);
        $iKirkPos = ($this->oKirk->x + $this->oKirk->y * $this->iCols);
        $indexMvt = $iNextPos - $iKirkPos;
        if ($indexMvt === -1) {
            return LEFT;
        }
        if ($indexMvt === 1) {
            return RIGHT;
        }
        if ($indexMvt === $this->iCols) {
            return DOWN;
        }
        if ($indexMvt === -$this->iCols) {
            return UP;
        }

        debug('Kirk is at: ' . $iKirkPos, $iNextPos, $aPath);
        throw new \Exception('Bad movement');
    }

    public function debugMap()
    {
        $aDebugMap = $this->aMap;
        $aDebugMap[$this->oKirk->y][$this->oKirk->x] = 'K';
        $sDebug = implode("\n", $aDebugMap);
        debug("\n" . $sDebug . "\n");
    }
}

/**
 * Class Kirk
 *
 * @package Le_Labyrinthe
 * @author Nicolas (niconoe) Giraud <nicolas.giraud.dev@gmail.com>
 * @copyright Copyright © 2015, Nicolas Giraud
 */
class Kirk
{
    public $x;
    public $y;

    public $prevMvt;

    public function initRoundPos()
    {
        fscanf(STDIN, "%d %d", $this->y, $this->x);
    }

    public function setPos($x, $y)
    {
        $this->x = $x;
        $this->y = $y;
        return $this;
    }

    public function move($mvt)
    {
        $this->prevMvt = $mvt;
        echo $mvt;
    }

    public function getPreviousCoordinates()
    {
        $xPrev = $this->x;
        $yPrev = $this->y;
        if ($this->prevMvt === LEFT) {
            $xPrev += 1;
        } elseif ($this->prevMvt === RIGHT) {
            $xPrev -= 1;
        } elseif ($this->prevMvt === UP) {
            $yPrev += 1;
        } else {
            $yPrev -= 1;
        }

        return [$xPrev, $yPrev];
    }

    public function getNextCoordinates($sMvt)
    {
        $xNext = $this->x;
        $yNext = $this->y;
        if ($sMvt === LEFT) {
            $xNext -= 1;
        } elseif ($sMvt === RIGHT) {
            $xNext += 1;
        } elseif ($sMvt === UP) {
            $yNext -= 1;
        } else {
            $yNext += 1;
        }

        return [$xNext, $yNext];
    }
}


class Main {

    public static $aReverse = [
        LEFT => RIGHT,
        RIGHT => LEFT,
        UP => DOWN,
        DOWN => UP,
    ];

    public static $bExplorationMode = true;
    public static $bReachCommandMode = false;
    public static $bBackToStart = false;
    public static $aFinalePath = [];
    public static $aPathGoingToEnd = [];
    public static $aVisited = [];

    /** @var $oLabyrinth Labyrinth */
    public static $oLabyrinth;

    /** @var $oKirk Kirk */
    public static $oKirk;

    public static $iPosKirk;

    public static function run()
    {
        self::$oLabyrinth = new Labyrinth();
        self::$oKirk = new Kirk();

        while (1) {
            self::round();
        }
    }

    public static function round()
    {
        $oKirk = self::$oKirk;
        $oLabyrinth = self::$oLabyrinth;

        $oKirk->initRoundPos();
        $oLabyrinth->initMap()
            ->placeKirk($oKirk)
            ->iNbRoundLeft--;

        self::$iPosKirk = $oKirk->x + $oKirk->y * $oLabyrinth->iCols;
        self::$aVisited[self::$iPosKirk] = $oLabyrinth->iNbRoundLeft;

        if (self::$bExplorationMode) {
            if (self::explore()) {
                return;
            }
        } elseif (empty(self::$aFinalePath)) {
            debug('Command center found!');
            //The center command is found so let's calculate the shortest path and compare with the number of steps
            //allowed.
            $aPath = $oLabyrinth->findShortestReturnPath();
            if ($aPath !== false) {
                self::$aFinalePath = $aPath;
                self::$bExplorationMode = false;
                self::$bReachCommandMode = true;
            } else {
                self::$bExplorationMode = true;
                if (self::explore()) {
                    return;
                }
            }
        }

        if (self::$bReachCommandMode) {

            //If Kirk reach the command, go back
            if ($oLabyrinth->isKirkAtCommand()) {
                debug('Here I am !');
                self::$bReachCommandMode = false;
                self::$bBackToStart = true;
            } else {
                if (empty(self::$aPathGoingToEnd)) {
                    debug('Finding path to command center !');
                    self::$aPathGoingToEnd = $oLabyrinth->findShortestKirkPath();
                }
                $sMove = $oLabyrinth->moveFollowingPath(self::$aPathGoingToEnd);
                $oKirk->move($sMove);
                return;
            }
        }

        if (self::$bBackToStart) {
            //Use the $aFinalePath to retrieve the smallest path to the start.
            $sMove = $oLabyrinth->moveFollowingPath(self::$aFinalePath);
            $oKirk->move($sMove);
            return;
        }
    }

    public static function explore()
    {
        //Get all possible direction Kirk can do
        $aPossibleMvt = self::$oLabyrinth->getPossibleMovementForKirk();
        //Remove the possibility of behind if exists
        if (
            self::$oKirk->prevMvt &&
            false !== ($key = array_search(self::$aReverse[self::$oKirk->prevMvt], $aPossibleMvt))
        ) {
            unset($aPossibleMvt[$key]);
        }

        //If there's no more possibility, we are in a dead-end so go back
        if (empty($aPossibleMvt)) {
            self::$oKirk->move(self::$aReverse[self::$oKirk->prevMvt]);
            debug('Go Back!');
            self::$bExplorationMode = !self::$oLabyrinth->isCommandCenterFound();
            return true;
        }

        //If only one movement possible, do it.
        if (count($aPossibleMvt) === 1) {
            debug('One direction!');
            self::$oKirk->move(reset($aPossibleMvt));
            self::$bExplorationMode = !self::$oLabyrinth->isCommandCenterFound();
            return true;
        }

        //Remove all visited cases
        $aPreviousPossibleMvt = $aPossibleMvt;
        foreach ($aPossibleMvt as $idx => $sMvt) {
            if ($sMvt === LEFT && isset(self::$aVisited[self::$iPosKirk - 1])) {
                unset($aPossibleMvt[$idx]);
            }
            if ($sMvt === RIGHT && isset(self::$aVisited[self::$iPosKirk + 1])) {
                unset($aPossibleMvt[$idx]);
            }
            if ($sMvt === UP && isset(self::$aVisited[self::$iPosKirk - self::$oLabyrinth->iCols])) {
                unset($aPossibleMvt[$idx]);
            }
            if ($sMvt === DOWN && isset(self::$aVisited[self::$iPosKirk + self::$oLabyrinth->iCols])) {
                unset($aPossibleMvt[$idx]);
            }
        }

        //If no possible movement left, choose the 1st visited case to go
        if (empty($aPossibleMvt)) {
            $aPossibleMvt = $aPreviousPossibleMvt;
            $aLeftVisited = [];
            foreach ($aPossibleMvt as $idx => $sMvt) {
                if ($sMvt === LEFT) {
                    $aLeftVisited[LEFT] = self::$aVisited[self::$iPosKirk - 1];
                }
                if ($sMvt === RIGHT) {
                    $aLeftVisited[RIGHT] = self::$aVisited[self::$iPosKirk + 1];
                }
                if ($sMvt === UP) {
                    $aLeftVisited[UP] = self::$aVisited[self::$iPosKirk - self::$oLabyrinth->iCols];
                }
                if ($sMvt === DOWN) {
                    $aLeftVisited[DOWN] = self::$aVisited[self::$iPosKirk + self::$oLabyrinth->iCols];
                }
            }
            list($sMove) = array_keys($aLeftVisited, max($aLeftVisited));
            self::$oKirk->move($sMove);
            self::$bExplorationMode = !self::$oLabyrinth->isCommandCenterFound();
            return true;
        }

        //If only one movement left, do it.
        if (count($aPossibleMvt) === 1) {
            debug('One direction left after trimming visited!');
            self::$oKirk->move(reset($aPossibleMvt));
            self::$bExplorationMode = !self::$oLabyrinth->isCommandCenterFound();
            return true;
        }

        //Otherwise, try to continue in the previous movement.
        if (in_array(self::$oKirk->prevMvt, $aPossibleMvt)) {
            debug('In front is not visited !');
            self::$oKirk->move(self::$oKirk->prevMvt);
            self::$bExplorationMode = !self::$oLabyrinth->isCommandCenterFound();
            return true;
        }

        //If only one movement still possible, do it.
        if (count($aPossibleMvt) === 1) {
            //If exists another path but already visited, rollback in mode
            foreach ([LEFT, RIGHT, UP, DOWN] as $sMvt) {
                if (reset($aPossibleMvt) === $sMvt || self::$aReverse[self::$oKirk->prevMvt] === $sMvt) {
                    continue;
                }
            }
            debug('Only one left after several choices !');
            self::$oKirk->move(reset($aPossibleMvt));
            return true;
        }

        //If not possible, go to the direction there's the most unknown case "?"
        if (null !== ($sOptimizedDir = self::$oLabyrinth->getOptimizedDirection($aPossibleMvt))) {
            self::$oKirk->move($sOptimizedDir);
            debug('Optimized choice !');
            return true;
        }

        return false;
    }

}

Main::run();
?>