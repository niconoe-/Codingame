<?php
define('DEBUG', true);
function debug() {if (!DEBUG) return; foreach (func_get_args() as $sArgDebug) error_log(var_export($sArgDebug, true));}

class Point
{
    public $x;
    public $y;
    
    public function __construct($x, $y)
    {
        $this->x = $x;
        $this->y = $y;
    }
}

class Ship
{
    const MAX_HS = 20;
    const MAX_VS = 40;
    
    public $oMap;
    
    public $coord;
    public $hs;
    public $vs;
    public $fuel;
    public $rotation;
    public $power;
    
    public $bIsLanding = false;
    public $bIsStarting = true;
    
    public $dx;
    
    public function initRound($oMap)
    {
        $this->oMap = $oMap;
        
        fscanf(STDIN, "%d %d %d %d %d %d %d",
            $X,
            $Y,
            $HS, // the horizontal speed (in m/s), can be negative.
            $VS, // the vertical speed (in m/s), can be negative.
            $F, // the quantity of remaining fuel in liters.
            $R, // the rotation angle in degrees (-90 to 90).
            $P // the thrust power (0 to 4).
        );
        
        $this->coord = new Point($X, $Y);
        $this->hs = $HS;
        $this->vs = $VS;
        $this->fuel = $F;
        $this->rotation = $R;
        $this->power = $P;
        
        $this->dx = $oMap->target->x - $X;
    }
    
    public function isRight()
    {
        return $this->dx > 0;
    }
    
    public function isAlign()
    {
        return $this->dx === 0;
    }
    
    public function move()
    {
        if ($this->bIsLanding) {
            return $this->land();
        }
        
        $r = 0;
        $p = 0;
        
        //If the ship has no good HS at start, give a 45° angle to the good direction until the HS is twice the max
        //to avoid too hard slowing down.
        //todo : This can be optimized by checking there's no obstacle and trying to rotate on more than 45°
        if ($this->bIsStarting && abs($this->hs) <= $this->getMaxHsForShip() && !$this->isAlign()) {
            $r = ($this->isRight() ? -45 : 45);
        } else {
            //Otherwise, we have started a landing approach. Calculate angle of slowing down.
            $this->bIsStarting = false;
            $r = $this->calculateAngle();
        }
        
        //Update power
        $p = ($this->vs > 0 && $this->coord->y > $this->oMap->target->y) ? 0 : $this->getOptimalPower();
        
        //Time for landing if HS <= 10 (each step power: 1+2+3+4) and dx <= 10
        if (abs($this->hs) <= 10 && abs($this->dx) <= 10) {
            $this->bIsLanding = true;
        }
        
        //Make sure that $r and $p are in the good intervals. We dont want $r < -45 and $r > 45 to avoir bad gravity attraction.
        if ($r < -45) {$r = -45;} elseif ($r > 45) {$r = 45;}
        if ($p < 0) {$p = 0;} elseif ($p > 4) {$p = 4;}
        
        $this->rotation = $r;
        $this->power = $p;
        return array($r, $p);
        
    }
    
    public function calculateAngle()
    {
        //If ship is align to middle of landing place, no rotation.
        if ($this->isAlign()) {
            return 0;
        }
        
        //Otherwise, calculus of the angle
        //Here is the formula:
        //
        //V(x)² = V0² + 2*a*(x-x0)
        //Where:
        // - x is the X value to reach
        // - x0 is the X position of the ship
        // - V(x) is the speed reached at x
        // - V0 is the current speed
        // - a is the acceleration (what we're looking for)
        //Note:
        // - (x-x0) is our property $dx
        // - V(x) is wanted to be 0 (no HS when ship is aligned)
        // - V0 is our property $hs
        //Indeed:
        //a = -($hs²) / (2*$dx)
        $a = -($this->hs * $this->hs) / (2*$this->dx);
        
        //Now, find the good angle. Here is the formula:
        //r = arctan(p/a) * 180/PI
        $r = round(atan($this->power / $a) * (180 / M_PI));
        //If acceleration is negative (ship is angled to mars' ground), turn back half turn
        if ($a < 0) {
            $r += 180;
        }
        //If middle of landing ground has been getting over, turn back
        if ($this->dx < 0 && $this->hs > 0 || $this->dx > 0 && $this->hs < 0) {
            $r = 90 - $r;
        }
        
        //Adjust the ship for Y axis
        $r -= 90;
        return $r;
    }
    
    
    public function getMaxHsForShip()
    {
        //Optimization: get the max HS depending on the distance between ship and landing place
        return 67;
        
    }
    
    public function getOptimalPower()
    {
        //Don't take care about gravity and fuel levels for now.
        //todo: return better value on calculations.
        return 4;
    }
    
    public function land()
    {
        $sureMaxVs = Ship::MAX_VS - 10; //10 for each step of power (1+2+3+4)
        $p = (abs($this->vs) > $sureMaxVs) ? $this->getOptimalPower() : 0;
        //Landing so $r needs to be 0
        $r = 0;
        return array($r, $p);
    }
}

class Map
{
    public $aPoints = array();
    public $target;
    public $xTarget;
    
    public function addPoint($x, $y)
    {
        $oPoint = new Point($x, $y);
        $this->aPoints[] = $oPoint;
        return $oPoint;
    }
    
    public function __construct()
    {
        // the number of points used to draw the surface of Mars.
        fscanf(STDIN, "%d", $N);
        // X coordinate of a surface point. (0 to 6999)
        // Y coordinate of a surface point. By linking all the points together in a sequential fashion, you form the surface of Mars.
        fscanf(STDIN, "%d %d", $X, $Y);
        $oPreviousPoint = $this->addPoint($X, $Y);
        for ($i = 1; $i < $N; ++$i) {
            fscanf(STDIN, "%d %d", $X, $Y);
            $oPoint = $this->addPoint($X, $Y);
            //If same Y value for previous and x has changed for about +1000, this is the landing place
            if ($oPreviousPoint->y === $oPoint->y && ($oPoint->x - $oPreviousPoint->x) >= 1000) {
                $xTarget = $this->getXTarget($oPreviousPoint->x, $oPoint->x);
                $this->target = new Point($xTarget, $oPoint->y);
            }
            //Landing place is unique so don't need to check if some is already defined.
            $oPreviousPoint = $oPoint;
        }
    }
    
    public function getXTarget($a, $b)
    {
        //Cut the landing space by the half
        $halfLanding = round(($b-$a)/2);
        //Add half of landing space to left X coord so middle of landing space is the xTarget
        return ($a + $halfLanding);
    }
}

$oMap = new Map();
$oShip = new Ship();

// game loop
while (TRUE)
{
    $oShip->initRound($oMap);
    list($r, $p) = $oShip->move();
    echo $r . ' ' . $p . "\n";
}
?>