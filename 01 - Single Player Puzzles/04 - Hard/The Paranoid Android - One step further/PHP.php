<?php
namespace TheParanoidAndroid_OneStepFurther;
define('DEBUG', true);
function debug() {if (!DEBUG) return; foreach (func_get_args() as $sArgDebug) error_log(var_export($sArgDebug, true));}

//define all output actions
define('WAIT', "WAIT\n");
define('ELEVATOR', "ELEVATOR\n");
define('BLOCK', "BLOCK\n");

/**
 * Class Game
 * Define initialisation values for the game.
 */
class Game
{
    public static $h;
    public static $w;

    public static $iElev;
    public static $iMoreElev;
    public static $iRounds;
    public static $iClones;

    public static function main()
    {
        $oExit = new Point();
        fscanf(STDIN, '%d %d %d %d %d %d %d %d',
            self::$h,           // number of floors
            self::$w,           // width of the area
            self::$iRounds,     // maximum number of rounds
            $oExit->y,          // floor on which the exit is found
            $oExit->x,          // position of the exit on its floor
            self::$iClones,     // number of generated clones
            self::$iMoreElev,   // number of additional elevators
            self::$iElev        // number of elevators
        );
        $oMap = new Map();
        $oMap->oExit = $oExit;
        $oMap->addElevators(); //Reads init input for elevators

        $bFirstClone = true;
        while (true) {
            //$oBot is the leading bot.
            $oBot = new Bot($oMap); //Reads input for every round

            if ($bFirstClone) {
                $bFirstClone = false;
                $iDirection = ($oBot->direction === 'RIGHT' ? 1 : -1);

                $oMap->getSolution(
                    0, $oBot->oCoordinates->x, $iDirection, self::$iMoreElev, self::$iClones, self::$iRounds
                );

                foreach ($oMap->aSolutionSteps as $oStep) {
                    debug($oStep->actionPoint . ': ' . trim($oStep->action));
                }
            }

            debug('Bot position: ' . $oBot->oCoordinates);
            if (empty($oMap->aSolutionSteps)) {
                echo WAIT;
                continue;
            }

            $oStep = reset($oMap->aSolutionSteps);
            if ($oBot->oCoordinates->x !== $oStep->actionPoint->x || $oBot->oCoordinates->y !== $oStep->actionPoint->y) {
                echo WAIT;
                continue;
            }

            echo $oStep->action;
            array_shift($oMap->aSolutionSteps);
        }
    }
}

/**
 * Class Point
 * Definition of a point in a 2 dimensional place.
 */
class Point
{
    /** @var int|null $x */
    public $x;

    /** @var int|null $y */
    public $y;

    /**
     * @param null|int $x The X coordinate of the point.
     * @param null|int $y The Y coordinate of the point.
     */
    public function __construct($x = null, $y = null)
    {
        $this->x = $x;
        $this->y = $y;
    }

    /**
     * Return the coordinates in the format "(x;y)"
     * @return string
     */
    public function __toString()
    {
        return ('(' . $this->x . ';' . $this->y . ')');
    }

    /**
     * Says if the Point given in argument is same coordinates as self Point
     * @param Point $oPoint The point to compare with self
     * @return bool True if coordinates are the same. False otherwise.
     */
    public function is(Point $oPoint)
    {
        return $this->x === $oPoint->x && $this->y === $oPoint->y;
    }

    /**
     * @return Point Return the coordinate up from self.
     */
    public function newUp()
    {
        return new self($this->x, $this->y+1);
    }

    /**
     * @return Point Return the coordinate down from self.
     */
    public function newDown()
    {
        return new self($this->x, $this->y-1);
    }

    /**
     * @return Point Return the coordinate left from self.
     */
    public function newLeft()
    {
        return new self($this->x-1, $this->y);
    }

    /**
     * @return Point Return the coordinate right from self.
     */
    public function newRight()
    {
        return new self($this->x+1, $this->y);
    }
}

/**
 * Class Step
 * Defines an important step for the solution (BLOCK or ELEVATOR)
 */
class Step
{
    /** @var Point $actionPoint */
    public $actionPoint;

    /** @var string $action */
    public $action;

    /**
     * @param Point $oPoint The position where action needs to be done
     * @param string $a Action to be done.
     */
    public function __construct(Point $oPoint, $a)
    {
        $this->actionPoint = $oPoint;
        $this->action = $a;
    }
}

class Map
{
    /** @var $oExit Point */
    public $oExit;

    /** @var $aElev int[][] */
    public $aElev = [];

    /** @var $aSolutionSteps Step[] */
    public $aSolutionSteps = [];

    public function addElevators()
    {
        for ($i = 0; $i < Game::$iElev; ++$i) {
            $oPoint = new Point();
            fscanf(STDIN, "%d %d", $oPoint->y, $oPoint->x);

            isset($this->aElev[$oPoint->y]) ?: $this->aElev[$oPoint->y] = [];
            $this->aElev[$oPoint->y] = array_merge($this->aElev[$oPoint->y], [$oPoint->x]);
        }
    }

    public function hasLift(Point $oPoint)
    {
        return isset($this->aElev[$oPoint->y]) && in_array($oPoint->x, $this->aElev[$oPoint->y]);
    }

    public function getSolution(
        $iFloor, $iPos, $iDir, $iLeftElevators, $iLeftClones, $iRoundsLeft
    )
    {
        //If current floor and position checking is not correct or if we don't have enough rounds, no solution found.
        if ($iFloor >= Game::$h || $iPos < 0 || $iRoundsLeft <= 0) {
            return false;
        }

        //If current floor and position is the exit, solution found.
        if ($this->oExit->is(new Point($iPos, $iFloor))) {
            $sDir = ($iDir === 1 ? 'RIGHT' : 'LEFT');
            $sMsg = 'Found from lift: %s -> %s. NbElevLeft: %d, NbClonesLeft: %d, NbRoundsLeft: %d.';
            debug(sprintf($sMsg, new Point($iPos, $iFloor), $sDir, $iLeftElevators, $iLeftClones, $iRoundsLeft));
            return true;
        }

        //If there's an elevator on the current floor and position, check if possible to get the solution from above.
        if ($this->hasLift(new Point($iPos, $iFloor))) {
            return $this->getSolution(
                $iFloor+1,
                $iPos,
                $iDir,
                $iLeftElevators,
                $iLeftClones,
                $iRoundsLeft-1
            );
        }

        //Going through every possible position according to the direction we're working on.
        $bSkipLift = false;
        for ($iSteps = 0, $i = $iPos; $i < Game::$w && $i >= 0; $i += $iDir, ++$iSteps) {
            //If there's an elevator on the current floor, try to find a solution from above this elevator.
            if ($this->hasLift(new Point($i, $iFloor))) {
                if (
                    $this->getSolution(
                        $iFloor+1,
                        $i,
                        $iDir,
                        $iLeftElevators,
                        $iLeftClones,
                        $iRoundsLeft - ($iSteps + 1)
                    )
                ) {
                    return true;
                }
                break;
            }

            //Try to move in the current direction. Hope we'll find the exit here.
            if ($this->oExit->y === $iFloor && $this->oExit->x === $i) {
                $sDir = ($iDir === 1 ? 'RIGHT' : 'LEFT');
                $sMsg = 'Found: %s -> %s. NbElevLeft: %d, NbClonesLeft: %d, NbRoundsLeft: %d.';
                debug(sprintf($sMsg, new Point($i, $iFloor+1), $sDir, $iLeftElevators, $iLeftClones, $iRoundsLeft));
                return true;
            }

            //If we're under an elevator, check solution from building an elevator, if possible
            $bUnderElevator = false;
            if ($iFloor < Game::$h && $this->hasLift(new Point($i, $iFloor+1))) {
                $bUnderElevator = true;
                $bSkipLift = false;
            }
            if (!$bSkipLift) {
                //Try to build an elevator and find the solution from this new point
                if (
                    $iLeftElevators > 0 &&
                    $this->getSolution(
                        $iFloor+1,
                        $i,
                        $iDir,
                        $iLeftElevators-1,
                        $iLeftClones,
                        $iRoundsLeft - ($iSteps + 4)
                    )
                ) {
                    $oStepPoint = new Point($i, $iFloor);
                    array_unshift($this->aSolutionSteps, new Step($oStepPoint, ELEVATOR));
                    debug('Action: ELEVATOR at ' . $oStepPoint . '.');
                    return true;
                }
                $bSkipLift = !$bUnderElevator;
            }
        }

        //No solution found on given floor in the current direction. Try to block to change direction.

        //If no clones left, impossible to change direction
        if ($iLeftClones <= 0) {
            return false;
        }

        $iBackPos = $iPos + $iDir;
        // $iSteps starts to 1 because turning back requires 1 step.
        for ($iSteps = 1, $i = $iPos - $iDir; $i < Game::$w && $i >= 0; $i -= $iDir, ++$iSteps) {
            //If there's an elevator on the current floor, try to find a solution from above this elevator.
            if ($this->hasLift(new Point($i, $iFloor))) {
                if (
                    $this->getSolution(
                        $iFloor+1,
                        $i,
                        -$iDir,
                        $iLeftElevators,
                        $iLeftClones - 1,
                        $iRoundsLeft - ($iSteps + 4)
                    )
                ) {
                    array_unshift($this->aSolutionSteps, new Step(new Point($iBackPos, $iFloor), BLOCK));
                    return true;
                }
                break;
            }

            //Try to move in the back direction. Hope we'll find the exit here.
            if ($this->oExit->y === $iFloor && $this->oExit->x === $i) {
                $sDir = ($iDir === 1 ? 'RIGHT' : 'LEFT');
                $sMsg = 'Found in back: %s -> %s. NbElevLeft: %d, NbClonesLeft: %d, NbRoundsLeft: %d.';
                debug(sprintf($sMsg, new Point($i, $iFloor+1), $sDir, $iLeftElevators, $iLeftClones, $iRoundsLeft));
                array_unshift($this->aSolutionSteps, new Step(new Point($iBackPos, $iFloor), BLOCK));
                return true;
            }

            //If we're under an elevator, check solution from building an elevator, if possible
            $bUnderElevator = false;
            if ($iFloor < Game::$h && $this->hasLift(new Point($i, $iFloor+1))) {
                $bUnderElevator = true;
                $bSkipLift = false;
            }
            if (!$bSkipLift) {
                //Try to build an elevator and find the solution from this new point
                if (
                    $iLeftElevators > 0 &&
                    $this->getSolution(
                        $iFloor+1,
                        $i,
                        -$iDir,
                        $iLeftElevators-1,
                        $iLeftClones-1,
                        $iRoundsLeft - ($iSteps + 8)
                    )
                ) {
                    array_unshift($this->aSolutionSteps, new Step(new Point($i, $iFloor), ELEVATOR));
                    array_unshift($this->aSolutionSteps, new Step(new Point($iBackPos, $iFloor), BLOCK));
                    return true;
                }
                $bSkipLift = !$bUnderElevator;
            }
        }

        return false;
    }
}

/**
 * Class Bot
 * Defines the leading clone bot we can manage.
 */
class Bot
{
    /** @var Point $oCoordinates */
    public $oCoordinates;

    /** @var string $direction */
    public $direction;

    /** @var Map $oMap */
    public $oMap;

    /**
     * @param Map $oMap The Map where evolves the leading bot.
     */
    public function __construct(Map $oMap)
    {
        $oCoordinates = new Point();
        fscanf(STDIN, '%d %d %s',
            $oCoordinates->y,   // floor of the leading clone
            $oCoordinates->x,   // position of the leading clone on its floor
            $this->direction    // direction of the leading clone: LEFT or RIGHT
        );
        $this->oCoordinates = $oCoordinates;
        $this->oMap = $oMap;
    }
}

Game::main();