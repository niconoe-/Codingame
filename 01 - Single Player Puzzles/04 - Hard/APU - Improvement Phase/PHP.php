<?php
namespace APU_Upgrade;

define('DEBUG', true);
function debug() {if (!DEBUG) return; foreach (func_get_args() as $sArgDebug) error_log(var_export($sArgDebug, true));}

/**
 * Class APU
 *
 * @package   APU_Upgrade
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

    public static $aNodes = [];

    /**
     * Start function. Only used to read the input data and then, call the function that solves the problem.
     */
    public static function run()
    {
        self::init();
        self::findNodes();
        do {
            debug(self::$aNodes);
            $b = self::determineEasyLinks();
            debug('---- END OF LOOP -----');
        } while($b);
        if (empty(self::$aNodes)) {
            return;
        }
        debug('More complicated links');
        debug(self::$aNodes);
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
     * Function that find all nodes of the map
     */
    public static function findNodes()
    {
        for ($y = 0; $y < self::$h; ++$y) {
            for ($x = 0; $x < self::$w; ++$x) {
                self::checkNode($x, $y, true);
            }
        }
    }

    /**
     * Check if the map defines the coordinate X and Y and if there's a node at these coordinates.
     * @param int $x The X coordinate
     * @param int $y The Y coordinate
     * @return bool TRUE if there's a node here. False if empty of node or outside the map.
     */
    public static function checkNode($x, $y, $init = false) {
        $i = self::indexOf($x, $y);
        if (isset(self::$aNodes[$i])) {
            return true;
        }
        if (isset(self::$aMap[$y][$x]) && self::$aMap[$y][$x] !== '.') {
            if ($init) {
                self::$aNodes[$i] = (int)self::$aMap[$y][$x];
            }
            return true;
        }
        return false;
    }

    /**
     * Search the first node encountered if we start from a node position and looking at right direction on the X axis.
     * @param int $x The X coordinate of the node to start
     * @param int $y The Y coordinate of the node to start
     * @return int|bool The coordinate of the first encountered node or false if no such node exists.
     */
    public static function searchNodeX($x, $y)
    {
        $iStep = 0;
        do {
            $b = self::checkNode($x+(++$iStep), $y);
        } while ($b === false && isset(self::$aMap[$y][$x+$iStep]));
        return ($b ? self::indexOf(($x+$iStep),$y) : false);
    }

    /**
     * Search the first node encountered if we start from a node position and looking at bottom direction on the Y axis.
     * @param int $x The X coordinate of the node to start
     * @param int $y The Y coordinate of the node to start
     * @return int|bool The coordinate of the first encountered node or false if no such node exists.
     */
    public static function searchNodeY($x, $y)
    {
        $iStep = 0;
        do {
            $b = self::checkNode($x, $y+(++$iStep));
        } while ($b === false && isset(self::$aMap[$y+$iStep][$x]));
        return ($b ? self::indexOf($x, ($y+$iStep)) : false);
    }

    public static function indexOf($x, $y)
    {
        return ($y * self::$w) + $x;
    }

    public static function coordOf($i)
    {
        return [$i%self::$w, floor($i/self::$w)];
    }

    public static function determineEasyLinks()
    {
        foreach (self::$aNodes as $i => $value) {
            debug($i);
            list($x, $y) = self::coordOf($i);
            if (self::searchEasyLinkX($x, $y, $value) || self::searchEasyLinkY($x, $y, $value)) {
                return true;
            }

        }
        return false;
    }

    public static function searchEasyLinkX($x, $y, $value)
    {
        if (false === ($iIndexNodeLink = self::searchNodeX($x, $y)) || !isset(self::$aNodes[$iIndexNodeLink])) {
            return false;
        }
        //if current node value is 1/2 or found node value is 1/2, the link has to be created.
        //Special case: the link must not be created if it remains some other nodes and if both are equals because the
        //solution will not form a connected graph
        for ($nbLinks=1; $nbLinks<3; ++$nbLinks) {
            if ($value === $nbLinks || self::$aNodes[$iIndexNodeLink] === $nbLinks) {
                if ($value === self::$aNodes[$iIndexNodeLink] && count(self::$aNodes) > 2) {
                    return false;
                }
                list ($xDest, $yDest) = self::coordOf($iIndexNodeLink);
                return self::printLink($x, $y, $xDest, $yDest, $nbLinks);
            }
        }
        return false;
    }

    public static function searchEasyLinkY($x, $y, $value)
    {
        if (false === ($iIndexNodeLink = self::searchNodeY($x, $y)) || !isset(self::$aNodes[$iIndexNodeLink])) {
            return false;
        }
        //if current node value is 1/2 or found node value is 1/2, the link has to be created.
        //Special case: the link must not be created if it remains some other nodes and if both are equals because the
        //solution will not form a connected graph
        for ($nbLinks=1; $nbLinks<3; ++$nbLinks) {
            if ($value === $nbLinks || self::$aNodes[$iIndexNodeLink] === $nbLinks) {
                if ($value === self::$aNodes[$iIndexNodeLink] && count(self::$aNodes) > 2) {
                    return false;
                }
                list ($xDest, $yDest) = self::coordOf($iIndexNodeLink);
                return self::printLink($x, $y, $xDest, $yDest, $nbLinks);
            }
        }
        return false;
    }

    public static function printLink($xa, $ya, $xb, $yb, $nbLinks)
    {
        echo (implode(' ', [$xa, $ya, $xb, $yb, $nbLinks])) . "\n";
        self::$aNodes[self::indexOf($xa, $ya)] -= $nbLinks;
        self::$aNodes[self::indexOf($xb, $yb)] -= $nbLinks;

        debug(self::indexOf($xa, $ya) . ' and ' . self::indexOf($xb, $yb) . ' : -' . $nbLinks);

        self::$aNodes = array_filter(self::$aNodes);
        return true;
    }

}

APU::run();
?>