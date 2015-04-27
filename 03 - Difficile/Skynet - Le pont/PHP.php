<?php
namespace Skynet_Le_Pont;

define('DEBUG', true);
function debug() {if (!DEBUG) return; foreach (func_get_args() as $sArgDebug) error_log(var_export($sArgDebug, true));}

define('SPEED', "SPEED\n");
define('SLOW', "SLOW\n");
define('JUMP', "JUMP\n");
define('WAIT', "WAIT\n");
define('UP', "UP\n");
define('DOWN', "DOWN\n");

/**
 * Class Main
 *
 * @package   Skynet_Le_Pont
 * @author Nicolas (niconoe) Giraud <nicolas.giraud.dev@gmail.com>
 * @copyright Copyright © 2015, Nicolas Giraud
 */
class Main
{
    /** @var int $nbBikes the amount of motorbikes to control */
    public static $nbBikes;

    /** @var int $nbBikesToSurvive the minimum amount of motorbikes that must survive */
    public static $nbBikesToSurvive;

    /**
     * @var array $aLines Lanes of the road.
     *                    A dot character . represents a safe space and a zero 0 represents a hole in the road.
     */
    public static $aLines = [];

    /** @var int $iSpeed the motorbikes' speed */
    public static $iSpeed;

    public static $aBikes = [];

    public static $x;

    public static $xLastHole = 0;


    //public static $test = [WAIT, WAIT, JUMP, SPEED, JUMP, WAIT];


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
            fscanf(STDIN, '%s', self::$aLines[$i]);
            self::$xLastHole = max(self::$xLastHole, strrpos(self::$aLines[$i], '0'));
        }
    }

    public static function round()
    {
        fscanf(STDIN, '%d', self::$iSpeed);
        for ($i=0; $i<self::$nbBikes; ++$i) {
            $oMotorBikes = new Motorbike();
            if ($oMotorBikes->bIsActive) {
                self::$aBikes[] = $oMotorBikes;
                self::$x = $oMotorBikes->x;
            }
        }

        debug('test', self::$xLastHole);
        echo SPEED;
        return;

    }

    public static function findNextHoles()
    {
        $aNextHoleByLines = [];
        foreach (self::$aLines as $sLine) {
            $aNextHoleByLines[] = strpos($sLine, '0', self::$x);
        }
        $aNextHoleByLines = array_filter($aNextHoleByLines);
        return $aNextHoleByLines;
    }

    public static function calculateJumpSize($aNextHoles)
    {
        $aJumps = [];
        foreach ($aNextHoles as $iLines => $xHole) {
            $nb = 0;
            do {
                ++$nb;
                $aJumps[$iLines] = $nb;
                $i = $xHole + $nb;
            } while (self::$aLines[$iLines][$i] === '0');
        }

        return (max($aJumps) + 1);
    }
}

/**
 * Class Motorbike
 *
 * @package   Skynet_Le_Pont
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


class Possibility
{
    public $x;
    public $aY;
    public $action;

    public function __construct($x, $aMotorbikes)
    {

    }


}

Main::run();
?>