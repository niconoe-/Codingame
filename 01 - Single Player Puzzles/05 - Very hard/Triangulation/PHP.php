<?php

namespace Triangulation;

//Standards
define('DEBUG', true);
function debug() {if (!DEBUG) return; foreach (func_get_args() as $sArgDebug) error_log(var_export($sArgDebug, true));}
function _($m) {echo $m."\n";}


/**
 * Class Point
 *
 * @package   Triangulation
 * @copyright Copyright 2015, Nicolas (niconoe) Giraud
 */
class Point
{
    public $x;
    public $y;

    /**
     * Constructor.
     * @param int $x The X coordinate of the Point
     * @param int $y The Y coordinate of the Point
     */
    public function __construct($x, $y)
    {
        $this->x = $x;
        $this->y = $y;
    }

    /**
     * @return string The X and Y coordinates of the point.
     */
    public function __toString()
    {
        return $this->x . ' ' . $this->y;
    }
}

/**
 * Class Building
 *
 * @package   Triangulation
 * @copyright Copyright 2015, Nicolas (niconoe) Giraud
 */
class Building
{
    public $width;
    public $height;

    /**
     * Constructor. Use the input to feed the width and height of the building.
     */
    public function __construct()
    {
        fscanf(STDIN, '%d %d', $this->width, $this->height);
    }
}

/**
 * Class Batman
 *
 * @package   Triangulation
 * @copyright Copyright 2015, Nicolas (niconoe) Giraud
 */
class Batman extends Point
{
    public $nbRounds;

    public $oPreviousPoint;

    /**
     * Constructor.
     * Use the input to feed the number of rounds before game over.
     * Use the input again to define the position of Batman.
     * @param null|int $x The X coordinate of Batman
     * @param null|int $y The Y coordinate of Batman
     */
    public function __construct($x = null, $y = null)
    {
        fscanf(STDIN, '%d', $this->nbRounds);
        if ($x === null && $y === null) {
            fscanf(STDIN, '%d %d', $x, $y);
        }
        parent::__construct($x, $y);
    }

    /**
     * Move Batman to the given point on the building
     * @param Point $oPoint The destination point to go to.
     * @return $this
     */
    public function moveTo(Point $oPoint)
    {
        $this->oPreviousPoint = new Point($this->x, $this->y);
        $this->x = (int)round($oPoint->x);
        $this->y = (int)round($oPoint->y);
        _($oPoint);
        return $this;
    }
}

$oBuilding = new Building();
$oBatman = new Batman();

while (true) {
    fscanf(STDIN, '%s', $sDistanceInfo);
    if ($sDistanceInfo === 'UNKNOWN') {
        $iMiddleZoneX = (int)abs(round($oBuilding->width-1 - $oBatman->x));
        $iMiddleZoneY = (int)abs(round($oBuilding->height-1 - $oBatman->y));

        $oDestination = new Point($iMiddleZoneX, $iMiddleZoneY);
        $oBatman->moveTo($oDestination);
        continue;
    }


    debug($sDistanceInfo);
    $oBatman->moveTo(new Point(rand(0, $oBuilding->width-1), rand(0, $oBuilding->height-1)));
}