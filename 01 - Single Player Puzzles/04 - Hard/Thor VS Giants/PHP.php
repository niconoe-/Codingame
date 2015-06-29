<?php

namespace ThorVSGiants;

//Standards
define('DEBUG', true);
function debug() {if (!DEBUG) return; foreach (func_get_args() as $sArgDebug) error_log(var_export($sArgDebug, true));}
function _($m) {echo $m."\n";}

//Definitions of constants
define('N', 'N');
define('S', 'S');
define('E', 'E');
define('W', 'W');
define('NE', 'NE');
define('SE', 'SE');
define('NW', 'NW');
define('SW', 'SW');
define('WAIT', 'WAIT');
define('STRIKE', 'STRIKE');
define('WIDTH', 40);
define('HEIGHT', 18);
define('SHOCKWAVE_SIZE', 4);

class Point {
    public $x;
    public $y;

    public function __construct($x, $y) {
        $this->x = $x;
        $this->y = $y;
    }

    public static function buildBarycenter($aGiants)
    {
        $oCenter = new self(0, 0);
        foreach ($aGiants as $oGiant) {
            $oCenter->x += $oGiant->x;
            $oCenter->y += $oGiant->y;
        }

        $nbGiants = count($aGiants);
        $oCenter->x = floor($oCenter->x / $nbGiants);
        $oCenter->y = floor($oCenter->y / $nbGiants);

        return $oCenter;
    }

    public function distance($oTarget)
    {
        return abs($this->x - $oTarget->x) + abs($this->y - $oTarget->y);
    }
}
class Person extends Point {
    public function __construct() {
        fscanf(STDIN, "%d %d", $this->x, $this->y);
    }

    public static function getBestAdvantage($oTarget, $aGiants)
    {
        if (!Giant::existsTooCloseGiants($oTarget->x, $oTarget->y, $aGiants)) {
            return [count(Giant::findCloseGiants($oTarget->x, $oTarget->y, $aGiants)), $oTarget];
        }
        return null;
    }
}

class Thor extends Person {
    public $hStrikes;

    public function findAction($aGiants)
    {
        $oCenter = Point::buildBarycenter($aGiants);

        //If no close giants, go to the center as required
        if (!Giant::existsTooCloseGiants($this->x, $this->y, $aGiants)) {
            return $this->findMove($oCenter);
        }

        //Otherwise, check in which direction it should be the best advantage. Each value of this will be an array
        //containing the number of close giants and the position on the map this advantage will take off.
        $aBestAdvantage = [];
        if ($this->x > 0) {
            $aBestAdvantage[W] = Person::getBestAdvantage(new Point($this->x - 1, $this->y), $aGiants);
        }
        if ($this->y > 0) {
            $aBestAdvantage[N] = Person::getBestAdvantage(new Point($this->x, $this->y - 1), $aGiants);
        }
        if ($this->x < WIDTH) {
            $aBestAdvantage[E] = Person::getBestAdvantage(new Point($this->x + 1, $this->y), $aGiants);
        }
        if ($this->y < HEIGHT) {
            $aBestAdvantage[S] = Person::getBestAdvantage(new Point($this->x, $this->y + 1), $aGiants);
        }
        if ($this->x > 0 && $this->y > 0) {
            $aBestAdvantage[NW] = Person::getBestAdvantage(new Point($this->x - 1, $this->y - 1), $aGiants);
        }
        if ($this->x < WIDTH && $this->y > 0) {
            $aBestAdvantage[NE] = Person::getBestAdvantage(new Point($this->x + 1, $this->y - 1), $aGiants);
        }
        if ($this->x > 0 && $this->y < HEIGHT) {
            $aBestAdvantage[SW] = Person::getBestAdvantage(new Point($this->x - 1, $this->y + 1), $aGiants);
        }
        if ($this->x < WIDTH && $this->y < HEIGHT) {
            $aBestAdvantage[SE] = Person::getBestAdvantage(new Point($this->x + 1, $this->y + 1), $aGiants);
        }

        //Now, find the best action to do.
        $sAction = STRIKE;
        $iBestDistance = 0;
        $aBestOption = [0, null];
        foreach ($aBestAdvantage as $sDirection => $aOption) {
            if (is_null($aOption)) {
                continue;
            }
            if (
                ($aOption[0] > $aBestOption[0]) ||
                ($aOption[0] === $aBestOption[0] && $aOption[1]->distance($oCenter) > $iBestDistance)
            ) {
                $aBestOption = $aOption;
                $sAction = $sDirection;
                $iBestDistance = $aBestOption[1]->distance($oCenter);
            }
        }

        if ($sAction !== STRIKE) {
            $this->x = $aBestOption[1]->x;
            $this->y = $aBestOption[1]->y;
        }

        return $sAction;
    }

    public function findMove($oTarget)
    {
        if ($oTarget->x > $this->x) {
            $this->x++;
            if ($oTarget->y > $this->y) {
                $this->y++;
                return SE;
            } elseif ($oTarget->y < $this->y) {
                $this->y--;
                return NE;
            } else {
                return E;
            }
        } elseif ($oTarget->x < $this->x) {
            $this->x--;
            if ($oTarget->y > $this->y) {
                $this->y++;
                return SW;
            } elseif ($oTarget->y < $this->y) {
                $this->y--;
                return NW;
            } else {
                return W;
            }
        } else {
            if ($oTarget->y > $this->y) {
                $this->y++;
                return S;
            } elseif ($oTarget->y < $this->y) {
                $this->y--;
                return N;
            } else {
                return WAIT;
            }
        }
    }
}

class Giant extends Person {

    public static $aGiants = [];
    public static $nbGiants = 0;

    public static function getAll()
    {
        self::$aGiants = [];
        for ($i = 0; $i < self::$nbGiants; ++$i) {
            $oGiant = new self();
            $idx = WIDTH*$oGiant->y + $oGiant->x;
            self::$aGiants[$idx] = $oGiant;
        }

        return self::$aGiants;
    }

    /**
     * Find the close giants based on a coordinate in X and Y. All close giants are then in the shock wave if Thor
     * strikes
     * @param int $x The X coordinate to check all giants distance
     * @param int $y The Y coordinate to check all giants distance
     * @param Giant[] $aGiants List of giants to check of. If empty, the list of all defined giant will be used.
     * @return array List of giants in the shock wave if Thor is gonna strike at the X;Y position.
     */
    public static function findCloseGiants($x, $y, $aGiants = array())
    {
        if (empty($aGiants)) {
            $aGiants = self::$aGiants;
        }

        $aCloseGiants = [];
        foreach ($aGiants as $oGiant) {
            if ((abs($oGiant->x - $x) <= SHOCKWAVE_SIZE) && (abs($oGiant->y - $y) <= SHOCKWAVE_SIZE)) {
                $aCloseGiants[] = $oGiant;
            }
        }

        return $aCloseGiants;
    }

    /**
     * Find if exists giants too close from the given position with X and Y coordinates
     * @param int $x The X coordinate to check all giants distance
     * @param int $y The Y coordinate to check all giants distance
     * @param Giant[] $aGiants List of giants to check of. If empty, the list of all defined giant will be used.
     * @return bool TRUE if exists any giant too close. FALSE otherwise.
     */
    public static function existsTooCloseGiants($x, $y, $aGiants = array())
    {
        if (empty($aGiants)) {
            $aGiants = self::$aGiants;
        }

        foreach ($aGiants as $oGiant) {
            if ((abs($oGiant->x - $x) <= 1) && (abs($oGiant->y - $y) <= 1)) {
                return true;
            }
        }
        return false;
    }


}

$oThor = new Thor();

// game loop
while (true) {
    fscanf(STDIN, "%d %d", $oThor->hStrikes, Giant::$nbGiants);
    $aGiants = Giant::getAll();

    $aCloseGiants = Giant::findCloseGiants($oThor->x, $oThor->y);

    //If we can kill all giants left, let's do it right now!
    if (count($aCloseGiants) === Giant::$nbGiants) {
        _(STRIKE);
        continue;
    }

    //Otherwise, try to make the expected action depending on the situation
    _($oThor->findAction($aGiants));

}
?>