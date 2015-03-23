<?php
define('DEBUG', true);
function debug() {if (!DEBUG) return; foreach (func_get_args() as $sArgDebug) error_log(var_export($sArgDebug, true));}

class Map
{
    public $map = '';
    public $w;
    public $h;
    public $len;
    
    public $readMap = array();
    public $aStack = [];
    
    public $iAnalyzeCurrentSize = 0;
    
    public function __construct()
    {
        //Map is w width to h height
        fscanf(STDIN, "%d", $this->w);
        fscanf(STDIN, "%d", $this->h);
        for ($i = 0; $i < $this->h; ++$i) {
            fscanf(STDIN, "%s", $row);
            $this->map .= $row;
        }
        $this->len = $this->w * $this->h;
    }
    
    public function analyzeInputPoint($n)
    {    
        //Testing coord (X;Y)
        fscanf(STDIN, "%d %d", $X, $Y);
        $inputPoint = $X + $this->w * $Y;
        if (!isset($this->readMap[$inputPoint])) {
            //Reset the current analyze
            unset($this->iAnalyzeCurrentSize);
            $this->iAnalyzeCurrentSize = 0;
            $this->addStack($inputPoint);
            $this->loop();
        }
        return $this->readMap[$inputPoint];
        
    }
    
    public function loop()
    {
        //Elements from the stack are not read and exists. They can only be indexes of "O".
        while (null !== ($i = array_shift($this->aStack))) {
            //Add up, down, left and right indexes if each indexes map of unread "O".
            
            //Do not add in stack if point is on the map
            if ($i+$this->w < $this->len) {
                $this->addStack($i+$this->w); //Down
            }
            if ($i-$this->w >= 0) {
                $this->addStack($i-$this->w); //Up
            }
            
            //Do not add in stack left or right if position is x=0 or x=width-1
            $x = $i % $this->w;
            if ($x !== 0) {
                //x!=0: add left position
                $this->addStack($i-1);
            }
            if ($x !== $this->w-1) {
                //x!=width-1: add right position
                $this->addStack($i+1);
            }
        }
    }
    
    /**
     * Add in stack if exists, unread and indexes "O"
     */
    public function addStack($i)
    {
        //$i must be a real index of the map.
        
        // Check unread
        if (isset($this->readMap[$i])) {
            return;
        }
        
        //Check water
        if ($this->map[$i] === "#") {
            $this->readMap[$i] = 0;
            return;
        }
        
        //Increase the size of the lake.
        ++$this->iAnalyzeCurrentSize;
        $this->readMap[$i] = &$this->iAnalyzeCurrentSize;
        $this->aStack[] = $i;
    }
}

$oMap = new Map();
//Number of coords to test
fscanf(STDIN, "%d", $N);
for ($i = 0; $i < $N; ++$i) {
    echo $oMap->analyzeInputPoint($i) . "\n";
}

?>