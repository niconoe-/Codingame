<?php
namespace SkyNet_The_Bridge;
define('DEBUG', true);
function debug() {if (!DEBUG) return; foreach (func_get_args() as $sArgDebug) error_log(var_export($sArgDebug, true));}

/**
 * Class Main
 *
 * @package   SkyNet_The_Bridge
 * @author Nicolas (niconoe) Giraud <nicolas.giraud.dev@gmail.com>
 * @copyright Copyright © 2015, Nicolas Giraud
 */
class Main
{
    public static $aCommands = ['SPEED', 'WAIT', 'JUMP', 'SLOW', 'UP', 'DOWN'];

    /** @var int $nbBikes the amount of motorbikes to control */
    public static $nbBikes;

    /** @var int $nbBikesToSurvive the minimum amount of motorbikes that must survive */
    public static $nbBikesToSurvive;

    /**
     * @var string[] $aRoads Lanes of the road.
     * A dot character . represents a safe space and a zero 0 represents a hole in the road.
     */
    public static $aRoads = [];

    /** @var int $iSpeed the motorbikes' speed */
    public static $iSpeed;

    /** @var Motorbike[] List of motorbikes in game */
    public static $aBikes = [];

    /** @var Solver Object that contains each step to the solution */
    public static $oSolver;

    public static function run()
    {
        self::init();
        while (true) {
            self::round();
        }
    }

    public static function init()
    {
        fscanf(STDIN, '%d', self::$nbBikes);
        fscanf(STDIN, '%d', self::$nbBikesToSurvive);

        //Road is 4 lanes width
        for ($i=0; $i<4; ++$i) {
            fscanf(STDIN, '%s', self::$aRoads[$i]);
        }
    }

    public static function round()
    {
        fscanf(STDIN, '%d', self::$iSpeed);
        for ($i=0; $i<self::$nbBikes; ++$i) {
            self::$aBikes[] = new Motorbike();
        }

        //If we don't have a solution, let's find it!
        if (self::$oSolver === null) {
            self::$oSolver = new Solver();
        }

        //We have the solution now, so display last step
        self::$oSolver->echoAndRemoveStep();
    }
}

/**
 * Class Motorbike
 *
 * @package   SkyNet_The_Bridge
 * @author Nicolas (niconoe) Giraud <nicolas.giraud.dev@gmail.com>
 * @copyright Copyright © 2015, Nicolas Giraud
 */
class Motorbike
{
    public $x; // $X coordinate of the motorbike
    public $y; // $Y coordinate of the motorbike

    public $bIsActive; // indicates whether the motorbike is activated (true) or destroyed (false)

    /**
     * Build a new Motorbike object with its coordinates in X and Y and set if the motorbike is active or not.
     */
    public function __construct()
    {
        fscanf(STDIN, '%d %d %d', $this->x, $this->y, $this->bIsActive);
        $this->bIsActive = (bool)$this->bIsActive;
    }
}

class Context
{
    /** @var int $iSpeed */
    public $iSpeed;

    /** @var Motorbike[] $aBikes */
    public $aBikes;

    public function __construct($iSpeed, $aBikes)
    {
        $this->iSpeed = $iSpeed;
        $this->aBikes = $aBikes;
    }

    public function __clone()
    {
        $newBikes = [];
        foreach ($this->aBikes as $oBike) {
            $newBikes[] = clone $oBike;
        }
        $this->aBikes = $newBikes;
    }

    /**
     * Returns TRUE if at least one bike reach the end of the road.
     * @return bool
     */
    public function isDone()
    {
        foreach ($this->aBikes as $oBike) {
            if ($oBike->x > strlen(Main::$aRoads[$oBike->y])) {
                return true;
            }
        }
        return false;
    }

    /**
     * Returns TRUE if enough active bikes in the current context.
     * @return bool
     */
    public function isValid()
    {
        $nbActives = 0;
        foreach ($this->aBikes as $oBike) {
            !$oBike->bIsActive ?: $nbActives++;
        }

        return $nbActives >= Main::$nbBikesToSurvive;
    }

    /**
     * Returns TRUE if there's a bike on top road on the current context
     * @return bool
     */
    public function existsTopBike()
    {
        foreach ($this->aBikes as $oBike) {
            if ($oBike->bIsActive && $oBike->y === 0) {
                return true;
            }
        }
        return false;
    }

    /**
     * Returns TRUE if there's a bike on bottom road on the current context
     * @return bool
     */
    public function existsBottomBike()
    {
        foreach ($this->aBikes as $oBike) {
            if ($oBike->bIsActive && $oBike->y === 3) {
                return true;
            }
        }
        return false;
    }
}


class Solver
{
    /** @var string[] $aSteps. List of steps to follow to find the solution. */
    public $aSteps = [];

    public function __construct()
    {
        $oContext = new Context(Main::$iSpeed, Main::$aBikes);
        $this->solve($oContext);
    }

    public function solve(Context $oContext)
    {
        if ($oContext->isDone()) {
            return true;
        }

        //Try all possible command to find a solution.
        foreach (Main::$aCommands as $sCommand) {
            //We just build a new context trying a command from a previous context.
            $oMovedContext = $this->move($oContext, $sCommand);

            //If this new context is valid, try to solve the puzzle from this new context.
            if ($oMovedContext->isValid()) {
                if ($this->solve($oMovedContext)) {
                    $this->aSteps[] = $sCommand;
                    return true;
                }
            } else {
            }
            //Otherwise, if this new context is not valid or impossible to solve, try another command.
        }

        return false;
    }

    public function move(Context $oContext, $sCommand)
    {
        $oMovedContext = clone $oContext;

        //Speed commands management
        if ($sCommand === 'SPEED') {
            $oMovedContext->iSpeed++;
        } elseif ($sCommand === 'SLOW' && $oMovedContext->iSpeed > 1) {
            $oMovedContext->iSpeed--;
        }

        //Movement commands
        foreach ($oMovedContext->aBikes as $oBike) {
            if (!$oBike->bIsActive) {
                continue;
            }

            if ($sCommand === 'SPEED' || $sCommand === 'SLOW' || $sCommand === 'WAIT') {
                $oBike->bIsActive = $this->checkAllGround($oBike, $oMovedContext->iSpeed, 0);
            } elseif ($sCommand === 'JUMP') {
                $oBike->bIsActive = $this->checkGround($oBike, $oMovedContext->iSpeed);
            } elseif ($sCommand === 'UP') {
                if (!$oContext->existsTopBike()) {
                    $oBike->bIsActive = $this->canChangeRoad($oBike, $oMovedContext->iSpeed, 'UP');
                    !$oBike->bIsActive ?: $oBike->y--;
                } else {
                    $oBike->bIsActive = $this->checkAllGround($oBike, $oMovedContext->iSpeed, 0);
                }
            } elseif ($sCommand === 'DOWN') {
                if (!$oContext->existsBottomBike()) {
                    $oBike->bIsActive = $this->canChangeRoad($oBike, $oMovedContext->iSpeed, 'DOWN');
                    !$oBike->bIsActive ?: $oBike->y++;
                } else {
                    $oBike->bIsActive = $this->checkAllGround($oBike, $oMovedContext->iSpeed, 0);
                }
            }

            if ($oBike->bIsActive) {
                $oBike->x += $oMovedContext->iSpeed;
            }
        }

        return $oMovedContext;
    }

    public function canChangeRoad(Motorbike $oBike, $iSpeed, $sDirection)
    {
        $iDelta = ($sDirection === 'UP') ? -1 : 1;
        return ($this->checkAllGround($oBike, $iSpeed-1, 0) && $this->checkAllGround($oBike, $iSpeed, $iDelta));
    }

    public function checkGround(Motorbike $oBike, $iSpeed)
    {
        //Are we reaching the end of the road?
        $bEndOfRoad = ($oBike->x + $iSpeed >= strlen(Main::$aRoads[$oBike->y]));

        //Are we on the ground?
        $bOnGround = (Main::$aRoads[$oBike->y]{$oBike->x + $iSpeed} === '.');

        return $bEndOfRoad || $bOnGround;
    }

    public function checkAllGround(Motorbike $oBike, $iSpeed, $yDelta)
    {
        for ($i = $oBike->x + 1; $i <= $oBike->x + $iSpeed; ++$i) {

            //Are we reaching the end of the road?
            if ($i >= strlen(Main::$aRoads[$oBike->y + $yDelta])) {
                return true;
            }

            //Are we on the ground?
            if (Main::$aRoads[$oBike->y + $yDelta]{$i} === '0') {
               return false;
            }
        }

        //All positions are ok for this bike on this road.
        return true;
    }

    public function echoAndRemoveStep()
    {
        if (empty($this->aSteps)) {
            return;
        }
        $sCurrentStep = array_pop($this->aSteps);
        echo $sCurrentStep . PHP_EOL;
    }
}

Main::run();