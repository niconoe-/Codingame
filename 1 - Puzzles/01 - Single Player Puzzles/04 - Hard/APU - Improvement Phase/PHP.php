<?php
namespace APU_Upgrade;

define('DEBUG', true);
function debug() {if (!DEBUG) return; foreach (func_get_args() as $sArgDebug) error_log(var_export($sArgDebug, true));}

/**
 * Class Game
 *
 * @package   APU_Upgrade
 * @author Nicolas (niconoe) Giraud <nicolas.giraud.dev@gmail.com>
 * @copyright Copyright © 2015, Nicolas Giraud
 */
class Game
{
    const UP = 'UP';
    const DOWN = 'DOWN';
    const LEFT = 'LEFT';
    const RIGHT = 'RIGHT';

    /** @var int $width */
    public static $width;

    /** @var int $height */
    public static $height;

    /** @var int $nbCells */
    public static $nbCells;

    public static function init()
    {
        // the number of cells on the X axis
        fscanf(STDIN, '%d', Game::$width);
        // the number of cells on the Y axis
        fscanf(STDIN, '%d', Game::$height);

        Game::$nbCells = Game::$width * Game::$height;
    }

    public static function main()
    {
        $oMap = new Map();
        $oAPU = new APU($oMap);
        $oResultMap = $oAPU->getSolution();
        if (!($oResultMap instanceof Map)) {
            return;
        }
        $oResultMap->applySolution(); //render the links.
    }

    /**
     * Change couple (x;y) to indexed position on game.
     * @param Point $oPoint The coordinates
     * @return int The indexed position.
     */
    public static function index(Point $oPoint)
    {
        return (int)($oPoint->y * self::$width + $oPoint->x);
    }

    /**
     * Change an indexed I to coordinates on game.
     * @param int $index The index
     * @return Point The coordinates.
     */
    public static function coordinates($index)
    {
        $y = floor($index / self::$width);
        $x = $index % self::$width;
        return new Point($x, $y);
    }

    /**
     * Check if the point exists on the map.
     * @param Point $oPoint
     * @return bool
     */
    public static function exists(Point $oPoint)
    {
        return ($oPoint->x >= 0 && $oPoint->x < self::$width && $oPoint->y >= 0 && $oPoint->y < self::$height);
    }

    /**
     * Write a line of the answer to apply a link
     * @param int $indexA Index of the node A to be linked with node B
     * @param int $indexB Index of the node B to be linked with node A
     * @param int $nbLinks Number of links to add between the two node.
     */
    public static function writeLine($indexA, $indexB, $nbLinks)
    {
        $oPointA = self::coordinates($indexA);
        $oPointB = self::coordinates($indexB);

        echo $oPointA->x . ' ' . $oPointA->y . ' ' . $oPointB->x . ' ' . $oPointB->y . ' ' . $nbLinks . PHP_EOL;
    }

    /**
     * Replacement for PHP 5.5 array_column
     * @see http://php.net/manual/fr/function.array-column.php
     * @param      $array
     * @param      $column_key
     * @param null $index_key
     * @return array
     */
    public static function array_column($array, $column_key, $index_key = null)
    {
        if ($index_key === null) {
            return array_map(function($element) use($column_key){return $element[$column_key];}, $array);
        }

        $arrayColumn = [];
        foreach ($array as $values) {
            $arrayColumn[$values[$index_key]] = $values[$column_key];
        }
        return $arrayColumn;
    }

}

/**
 * Class Point
 * Definition of a point in a 2 dimensional place.
 *
 * @package APU_Upgrade
 * @author Nicolas (niconoe) Giraud <nicolas.giraud.dev@gmail.com>
 * @copyright Copyright © 2015, Nicolas Giraud
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
        return new self($this->x, $this->y-1);
    }

    /**
     * @return Point Return the coordinate down from self.
     */
    public function newDown()
    {
        return new self($this->x, $this->y+1);
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
 * Class Map
 *
 * @package   APU_Upgrade
 * @author Nicolas (niconoe) Giraud <nicolas.giraud.dev@gmail.com>
 * @copyright Copyright © 2015, Nicolas Giraud
 */
class Map
{
    const HORIZONTAL_CROSS = 'H';
    const VERTICAL_CROSS = 'V';

    /** @var string[] $aMap Map containing all nodes to link. */
    public $aMap = [];

    /** @var int[] $aNodesLinks Number of links we can still add to a node, indexed by position. */
    public $aNodesLinks = [];

    /** @var int[] $aNodesGroup List of group nodes. As the graph must be complete, there must stay only one group. */
    public $aNodesGroup = [];

    /** @var string[] $aCellsCrossed List of cells that are not nodes but contain a cross in a direction. */
    public $aCellsCrossed = [];

    /** @var int[][][] $aLinks Number of links set and blocked between two indexed nodes. */
    public $aLinks = [];

    /**
     * Map Constructor
     */
    public function __construct()
    {
        Game::init();

        for ($i = 0; $i < Game::$height; ++$i) {
            // width characters, each either 0 or .
            $this->aMap[] = str_split(stream_get_line(STDIN, 31, "\n"));
        }

        $this->initNodesLink();
    }

    public function getValue()
    {
        $aArgs = func_get_args();
        if (count($aArgs) > 2) {
            throw new \InvalidArgumentException('Map::getValue have no more than 2 parameters.');
        } elseif (count($aArgs) === 0) {
            throw new \InvalidArgumentException('Map::getValue must have at least 1 parameter.');
        }

        if (count($aArgs) === 2) {
            list($x, $y) = $aArgs;
            $oPoint = new Point($x, $y);
        } elseif ($aArgs[0] instanceof Point) {
            $oPoint = $aArgs[0];
        } elseif (is_numeric($aArgs[0])) {
            $index = (int)$aArgs[0];
            $oPoint = Game::coordinates($index);
        } else {
            throw new \InvalidArgumentException('Map::getValue parameter is not correct.');
        }

        if (!isset($this->aMap[$oPoint->y])) {
            throw new \InvalidArgumentException('Map::getValue parameter "Y" is not correct.');
        }
        if (!isset($this->aMap[$oPoint->y][$oPoint->x])) {
            throw new \InvalidArgumentException('Map::getValue parameter "X" is not correct.');
        }
        return $this->aMap[$oPoint->y][$oPoint->x];
    }

    public function initNodesLink()
    {
        $idNetGroup = 0;
        for ($i = 0; $i < Game::$nbCells; ++$i) {
            if (($iNbLinkToNode = $this->getValue($i)) === '.') {
                continue;
            }

            $this->aNodesLinks[$i] = (int)$iNbLinkToNode;
            $this->aNodesGroup[$i] = $idNetGroup++;
        }

    }

    /**
     * Get the number of free links that remain on the given indexed node.
     * @param int $index Index position on the map where the node is.
     * @return bool|int Number of free link that remains, False if this is not a node.
     */
    public function getNodeLinks($index)
    {
        return (isset($this->aNodesLinks[$index])) ? $this->aNodesLinks[$index] : false;
    }

    /**
     * Check if the graph of all nodes is complete, which means only one group is required.
     * @return bool
     */
    public function checkCompleteGraph()
    {
        $aDifferentGroups = array_unique($this->aNodesGroup);
        return (count($aDifferentGroups) === 1);
    }

    /**
     * Returns true if any node still has free links. False otherwise.
     * @return bool
     */
    public function checkFreeNodes()
    {
        foreach ($this->aNodesLinks as $nbFreeLinks) {
            if ($nbFreeLinks !== 0) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if exists several and dissociated groups of node.
     * @return bool True if several groups are existing, false if only one group remains, which is good.
     */
    public function existsSeveralGroups()
    {
        $aOpenGroups = [];
        foreach ($this->aNodesLinks as $index => $nbFreeLinks) {
            $idNetGroup = $this->aNodesGroup[$index];
            if (!array_key_exists($idNetGroup, $aOpenGroups)) {
                $aOpenGroups[$idNetGroup] = false;
            }
            if ($nbFreeLinks !== 0) {
                $aOpenGroups[$idNetGroup] = true;
            }
        }

        if (count($aOpenGroups) === 1) {
            return false;
        }

        return in_array(false, $aOpenGroups);
    }

    /**
     * Find the node next the the indexed one, base on a direction.
     * @param int $index Index position of the current node
     * @param string $sDir The direction to follow to find a node
     * @return Point|bool Coordinates of a node if found, FALSE otherwise.
     */
    public function findNextNode($index, $sDir)
    {
        switch ($sDir) {
            case Game::UP:
                $sMovingMethod = 'newUp';
                $sDoNotCross = Map::HORIZONTAL_CROSS;
                break;
            case Game::DOWN:
                $sMovingMethod = 'newDown';
                $sDoNotCross = Map::HORIZONTAL_CROSS;
                break;
            case Game::LEFT:
                $sMovingMethod = 'newLeft';
                $sDoNotCross = Map::VERTICAL_CROSS;
                break;
            case Game::RIGHT:
                $sMovingMethod = 'newRight';
                $sDoNotCross = Map::VERTICAL_CROSS;
                break;
            default:
                throw new \InvalidArgumentException('Direction while finding node is not correct');
        }

        $oNode = Game::coordinates($index);
        $oNode = $oNode->$sMovingMethod();
        while (Game::exists($oNode)) {
            //If we found a node, get its coordinates
            if ($this->getValue($oNode) !== '.') {
                return $oNode;
            }

            //Otherwise, check if we encounter a crossed cell
            $i = Game::index($oNode);
            if (isset($this->aCellsCrossed[$i]) && $sDoNotCross === $this->aCellsCrossed[$i]) {
                return false;
            }
            $oNode = $oNode->$sMovingMethod();
        }

        return false;
    }

    /**
     * Find the free next node from an indexed one.
     * @param int $index Index of the node we're trying to get the free node we could link to.
     * @return Point|bool Coordinates of a free node if found, FALSE otherwise.
     */
    public function findNextFreeNode($index)
    {
        $aDirectionsForLinks = [Game::RIGHT, Game::DOWN, Game::LEFT, Game::UP];

        foreach ($aDirectionsForLinks as $sDirection) {
            $oNode = $this->findNextNode($index, $sDirection);
            if (false !== $oNode && false != $this->countRemainingFreeLinksBetweenNodes($index, Game::index($oNode))) {
                return $oNode;
            }
        }
        return false;
    }

    /**
     * Count the number of links it remains from two nodes.
     * @param int $indexNodeA Index of the node A we're counting the free links.
     * @param int $indexNodeB Index of the node B we're counting the free links.
     * @return bool|int False if no free links. The number of free links if they have.
     */
    public function countRemainingFreeLinksBetweenNodes($indexNodeA, $indexNodeB)
    {
        if (false === $this->getNodeLinks($indexNodeA)) {
            return false;
        }
        if (false === ($iRemainingFreeLink = $this->getNodeLinks($indexNodeB))) {
            return false;
        }

        $iNbLinksBetweenNodesSet = $this->countLinksBetweenNodes($indexNodeA, $indexNodeB, 'set');
        $iNbLinksBetweenNodesBlocked = $this->countLinksBetweenNodes($indexNodeA, $indexNodeB, 'blocked');
        $iMinRemained = 2 - $iNbLinksBetweenNodesSet - $iNbLinksBetweenNodesBlocked;

        return min($iMinRemained, $iRemainingFreeLink);
    }

    /**
     * Count the defined links between two nodes. Links can be "set" or "blocked".
     * @param int $indexA Index of the node A we're counting the defined links
     * @param int $indexB Index of the node B we're counting the defined links
     * @param string $sMode Must be "set" or "blocked". Type of link we're counting.
     * @return int Number of defined links for the given mode, between the two given nodes.
     */
    public function countLinksBetweenNodes($indexA, $indexB, $sMode)
    {
        if (isset($this->aLinks[$indexA][$indexB])) {
            return $this->aLinks[$indexA][$indexB][$sMode];
        }

        if (isset($this->aLinks[$indexB][$indexA])) {
            return $this->aLinks[$indexB][$indexA][$sMode];
        }

        return 0;
    }

    /**
     * Add a link between to nodes. The link will be added as a "set" link.
     * @param int $indexA Index of the node A we're counting the defined links
     * @param int $indexB Index of the node B we're counting the defined links
     * @return int Number of defined links between the two given nodes.
     */
    public function addLink($indexA, $indexB)
    {
        $nbLink = 1;
        if (isset($this->aLinks[$indexA][$indexB]['set'])) {
            $nbLink = ++$this->aLinks[$indexA][$indexB]['set'];
        } elseif (isset($this->aLinks[$indexB][$indexA]['set'])) {
            $nbLink = ++$this->aLinks[$indexB][$indexA]['set'];
        } else {
            $this->aLinks[$indexA][$indexB]['set'] = 1;
            $this->aLinks[$indexA][$indexB]['blocked'] = 0;
        }

        --$this->aNodesLinks[$indexA];
        --$this->aNodesLinks[$indexB];

        $this->updateGraph($this->aNodesGroup[$indexA], $this->aNodesGroup[$indexB]);
        $this->updateCrossCells($indexA, $indexB);

        return $nbLink;
    }

    /**
     * Add a link between to nodes. The link will be added as a "blocked" link.
     * @param int $indexA Index of the node A we're counting the defined links
     * @param int $indexB Index of the node B we're counting the defined links
     * @return Number of defined links between the two given nodes.
     */
    public function addBlock($indexA, $indexB)
    {
        $nbBlock = 1;
        if (isset($this->aLinks[$indexA][$indexB]['blocked'])) {
            $nbBlock = ++$this->aLinks[$indexA][$indexB]['blocked'];
        } elseif (isset($this->aLinks[$indexB][$indexA]['blocked'])) {
            $nbBlock = ++$this->aLinks[$indexB][$indexA]['blocked'];
        } else {
            $this->aLinks[$indexA][$indexB]['set'] = 0;
            $this->aLinks[$indexA][$indexB]['blocked'] = 1;
        }

        return $nbBlock;
    }

    /**
     * Update the graph so the two nodes in arguments will now be part of the same group.
     * @param int $idGroupA Id of the group where belongs the node A
     * @param int $idGroupB Id of the group where belongs the node B
     */
    public function updateGraph($idGroupA, $idGroupB)
    {
        foreach ($this->aNodesGroup as &$idGroup) {
            if ($idGroupA === $idGroup) {
                $idGroup = $idGroupB;
            }
        } unset($idGroup);
    }

    /**
     * Update the map to define cells that only contains links, without nodes.
     * This because links cannot cross them-selves
     * @param int $indexA Index of the node A we're linking
     * @param int $indexB Index of the node B we're linking
     */
    public function updateCrossCells($indexA, $indexB)
    {
        $oPointA = Game::coordinates($indexA);
        $oPointB = Game::coordinates($indexB);
        $way = ($oPointA->y === $oPointB->y ? Map::HORIZONTAL_CROSS : Map::VERTICAL_CROSS);

        for ($y = min($oPointA->y, $oPointB->y), $yEnd = max($oPointA->y, $oPointB->y); $y <= $yEnd; ++$y) {
            for ($x = min($oPointA->x, $oPointB->x), $xEnd = max($oPointA->x, $oPointB->x); $x <= $xEnd; ++$x) {
                $this->aCellsCrossed[Game::index(new Point($x, $y))] = $way;
            }
        }
    }

    public function applySolution()
    {
        foreach ($this->aLinks as $indexA => $aLinks) {
            foreach ($aLinks as $indexB => $aNbLinks) {
                if ($aNbLinks['set'] === 0) {
                    continue;
                }
                Game::writeLine($indexA, $indexB, $aNbLinks['set']);
            }
        }
    }
}

/**
 * Class APU
 *
 * @package   APU_Upgrade
 * @author Nicolas (niconoe) Giraud <nicolas.giraud.dev@gmail.com>
 * @copyright Copyright © 2015, Nicolas Giraud
 */
class APU
{
    /** @var Map $oMap */
    public $oMap;

    /**
     * Build the APU game by giving the map.
     * @param Map $oMap
     */
    public function __construct(Map $oMap)
    {
        $this->oMap = $oMap;
    }

    /**
     * Get the Map with a maximum of links defined between node to complete the solution.
     * @return Map
     */
    public function getSolution()
    {
        while (true) {
            $bAddedLink = false;
            foreach ($this->oMap->aNodesLinks as $index => $nbFreeLinks) {
                if ($nbFreeLinks === 0) {
                    continue;
                }
                $bAddedLink = $this->createLinks($index) || $bAddedLink;
            }

            //If we can't add links to the map anymore
            if (!$bAddedLink) {
                //Then, if there's still some nodes with free links, guess part.
                if ($this->oMap->checkFreeNodes()) {
                    return $this->makeAssumptions();
                }
                break;
            }
        }

        return $this->oMap;
    }

    /**
     * Take care of creating a link, if possible, between the given index of a node and one that match with.
     * @param int $index The index of a node to link some other node with.
     * @return bool True if link was added. False otherwise.
     */
    public function createLinks($index)
    {
        $iRemainingLinks = $this->oMap->getNodeLinks($index);

        if (false == $iRemainingLinks) {
            return false;
        }
        $aDirectionsForLinks = [
            Game::RIGHT => ['node' => null, 'nbFree' => 0],
            Game::DOWN => ['node' => null, 'nbFree' => 0],
            Game::LEFT => ['node' => null, 'nbFree' => 0],
            Game::UP => ['node' => null, 'nbFree' => 0],
        ];

        foreach ($aDirectionsForLinks as $sDirection => &$aInfo) {
            $oNode = $this->oMap->findNextNode($index, $sDirection);
            if (false !== $oNode) {
                $aInfo['nbFree'] = $this->oMap->countRemainingFreeLinksBetweenNodes($index, Game::index($oNode));
            }
            $aInfo['node'] = $oNode;
        } unset($aInfo);

        $iNbTotalFree = array_sum(Game::array_column($aDirectionsForLinks, 'nbFree'));
        $iNbBlockedLinks = $iNbTotalFree - $iRemainingLinks;

        if ($iNbBlockedLinks >= 2) {
            return false;
        }

        $bHasAdded = false;
        foreach ($aDirectionsForLinks as $sDirection => &$aInfo) {
            if ($aInfo['nbFree'] !== 0) {
                /** @var Point $oNode */
                $oNode = $aInfo['node'];
                for ($i = $aInfo['nbFree'] - $iNbBlockedLinks; $i > 0; --$i) {
                    $this->oMap->addLink($index, Game::index($oNode));
                    $bHasAdded = true;
                }
            }
        } unset($aInfo);

        return $bHasAdded;
    }

    /**
     * Make assumptions when impossible to be sure a link can be applied on a map.
     * @return Map|bool
     */
    public function makeAssumptions()
    {
        /**@var Map $oBestMap */
        do {
            $bHasSeveralGroups = false;

            //If the node to link is set, it was defined by the previous loop passage, but unable to set a link.
            //So, block it and try to find a new solution with this knowledge.
            if (isset($oNodeToLink, $indexFoundNode)) {
                $this->oMap->addBlock($indexFoundNode, Game::index($oNodeToLink));
                return $this->getSolution();
            }

            //Node to link with another one, if possible.
            $oNodeToLink = false;
            $indexFoundNode = false;

            //Find nodes we can link with the above one.
            foreach ($this->oMap->aNodesLinks as $index => $nbLeftLinks) {
                //If the node is already done, continue.
                if ($nbLeftLinks === 0) {
                    continue;
                }

                //If the node has no way to be linked with another one now, continue
                if (false === ($oNodeToLink = $this->oMap->findNextFreeNode($index))) {
                    continue;
                }

                //We just fond a node!
                $indexFoundNode = $index;
                break;
            }

            if ($oNodeToLink === false) {
                return false;
            }

            //Start assumption with the node.
            $oCloneMap = clone($this->oMap);
            $oCloneMap->addLink($indexFoundNode, Game::index($oNodeToLink));
            $oNewAPU = new static($oCloneMap);
            !$oCloneMap->existsSeveralGroups() ?: $bHasSeveralGroups = true;
        } while (
            $bHasSeveralGroups ||
            false === ($oBestMap = $oNewAPU->getSolution()) ||
            $bNewMapSeveralGroups = (false === $oBestMap->checkCompleteGraph())
        );

        return $bNewMapSeveralGroups ? false : $oBestMap;
    }

}
Game::main();
