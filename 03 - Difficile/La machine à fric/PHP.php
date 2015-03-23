<?php
define('DEBUG', true);
function debug() {if (!DEBUG) return; foreach (func_get_args() as $sArgDebug) error_log(var_export($sArgDebug, true));}

class Dijkstra {

    public $visited = array();
    public $distance = array();
    public $startnode = null;
    public $map = array();
    public $infiniteDistance = -1;
    public $numberOfNodes = 0;
    public $bestPath = 0;
    public $matrixWidth = 0;

    public function __construct(&$ourMap, $infiniteDistance) {
        $this->infiniteDistance = $infiniteDistance;
        $this->map = &$ourMap;
        $this->numberOfNodes = count($ourMap);
        $this->bestPath = 0;
    }

    public function findLongestPath($start, $to = null) {
        $this->startnode = $start;
        foreach ($this->map as $node => $aLinks) {
            if ($node === $this->startnode) {
                $this->visited[$node] = true;
                $this->distance[$node] = -1;
            } else {
                $this->visited[$node] = false;
                $this->distance[$node] = isset($this->map[$this->startnode][$node]) 
                    ? $this->map[$this->startnode][$node] 
                    : $this->infiniteDistance;
            }
        }
        
        foreach ($this->map as $node => $aChildren) {
            if ($this->distance[$node] === $this->infiniteDistance) {
                continue;
            }
            $this->updateDistance($this->distance, $node);
            $this->visited[$node] = true;
        }
    }
    
    public function updateDistance(&$ourDistance, $obp) {
        if (!isset($this->map[$obp])) {
            return;
        }
        foreach ($this->map[$obp] as $node => $value) {
            if (
                ($value != $this->infiniteDistance) &&
                (($ourDistance[$obp] + $value) > $ourDistance[$node])
            ) {    
                $ourDistance[$node] = $ourDistance[$obp] + $value;
                if ($this->visited[$node]) {
                    $this->updateDistance($ourDistance, $node);
                }
            }
        }
    }

    public function getDistance($node) 
    {
        if (!isset($this->distance[$node]) || $this->distance[$node] <= $this->infiniteDistance) {
            return $this->infiniteDistance;
        }
        return $this->distance[$node];
    }
    
} // end class 


$aMap = [];
fscanf(STDIN, "%d", $N);
for ($i = 0; $i < $N; $i++) {
    list($room, $value, $exit1, $exit2) = explode(' ', stream_get_line(STDIN, 256, "\n"));
    $aMap[$room][$exit1] = intval($value);
    $aMap[$room][$exit2] = intval($value);
    $aMap[$room][$room] = 0;
}
$aMap['E']['E'] = 0; //The exit is a node that only link itself.


$oDijk = new Dijkstra($aMap, -1);

$oDijk->findLongestPath(0);
$sResult = $oDijk->getDistance('E');

echo $sResult . "\n";
?>