<?php
namespace APU_Init;

define('DEBUG', true);
function debug() {if (!DEBUG) return; foreach (func_get_args() as $sArgDebug) error_log(var_export($sArgDebug, true));}

/**
 * Class APU
 *
 * @package   APU_Init
 * @author Nicolas (niconoe) Giraud <nicolas.giraud.dev@gmail.com>
 * @copyright Copyright © 2015, Nicolas Giraud
 */
class APU
{
    /**
     * @var array The map that contains nodes
     */
    public static $aMap = [];

    /**
     * @var int The width of the map
     */
    public static $w;

    /**
     * @var int the height of the map
     */
    public static $h;

    /**
     * Start function. Only used to read the input data and then, call the function that solves the problem.
     */
    public static function run()
    {
        self::init();
        self::findLinks();
    }

    /**
     * Init function. Reads the input data and do some specific helping treatments.
     */
    public static function init()
    {
        // the number of cells on the X axis
        fscanf(STDIN, "%d", self::$w);
        // the number of cells on the Y axis
        fscanf(STDIN, "%d", self::$h);

        for ($i = 0; $i < self::$h; ++$i) {
            // width characters, each either 0 or .
            self::$aMap[] = stream_get_line(STDIN, 31, "\n");
        }
    }

    /**
     * Function that find all links to nodes and so, solves the problem.
     */
    public static function findLinks()
    {
        for ($y = 0; $y < self::$h; ++$y) {
            for ($x = 0; $x < self::$w; ++$x) {
                if (!self::checkNode($x, $y)) {
                    continue;
                }

                //Until found a node, look at          right          and at            bottom.
                echo $x . ' ' . $y . ' ' . self::searchNodeX($x, $y) . ' ' . self::searchNodeY($x, $y) . "\n";
            }
        }
    }

    /**
     * Check if the map defines the coordinate X and Y and if there's a node at these coordinates.
     * @param int $x The X coordinate
     * @param int $y The Y coordinate
     * @return bool TRUE if there's a node here. False if empty of node or outside the map.
     */
    public static function checkNode($x, $y) {
        return (isset(self::$aMap[$y][$x]) && self::$aMap[$y][$x] === '0');
    }

    /**
     * Search the first node encountered if we start from a node position and looking at right direction on the X axis.
     * @param int $x The X coordinate of the node to start
     * @param int $y The Y coordinate of the node to start
     * @return string The coordinate of the first encountered node or "-1 -1" if no such node exists.
     */
    public static function searchNodeX($x, $y)
    {
        $iStep = 0;
        do {
            $b = self::checkNode($x+(++$iStep), $y);
        } while ($b === false && isset(self::$aMap[$y][$x+$iStep]));
        return ($b ? (($x+$iStep) . ' ' . $y) : '-1 -1');
    }

    /**
     * Search the first node encountered if we start from a node position and looking at bottom direction on the Y axis.
     * @param int $x The X coordinate of the node to start
     * @param int $y The Y coordinate of the node to start
     * @return string The coordinate of the first encountered node or "-1 -1" if no such node exists.
     */
    public static function searchNodeY($x, $y)
    {
        $iStep = 0;
        do {
            $b = self::checkNode($x, $y+(++$iStep));
        } while ($b === false && isset(self::$aMap[$y+$iStep][$x]));
        return ($b ? ($x . ' ' . ($y+$iStep)) : '-1 -1');
    }

}

APU::run();
?>