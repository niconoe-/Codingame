<?php
define('DEBUG', true);
function debug() {if (!DEBUG) return; foreach (func_get_args() as $sArgDebug) error_log(var_export($sArgDebug, true));}

class Stations
{
    public $aStops = [];
    public $nbStations = 0;
    
    public function __construct()
    {
        fscanf(STDIN, "%d", $this->nbStations);
        for ($i = 0; $i < $this->nbStations; ++$i) {
            $oStop = Station::createInstance();
            $this->aStops[$oStop->ref] = $oStop;
        }
    }
    
    public static function findDistance(Station $a, Station $b)
    {
        $x = ($b->long - $a->long) * cos(($a->lat + $b->lat)/2);
        $y = $b->lat - $a->lat;
        $d = 6371 * sqrt($x*$x + $y*$y);
        
        return $d;
    }
    
    public function findStation($ref)
    {
        return $this->aStops[$ref];
    }
}

class Station
{
    public $id;
    public $ref;
    public $name;
    public $lat;
    public $long;
    
    public static function createInstance()
    {
        $oSelf = new self();
        list($oSelf->ref, $oSelf->name, , $oSelf->lat, $oSelf->long, , , , ) = explode(',', stream_get_line(STDIN, 256, "\n"));
        //Remove trailing '"' at start and end of the name
        $oSelf->name = substr($oSelf->name, 1, -1);
        //Set latitude and longitude in radians (x*PI/180)
        $oSelf->lat = $oSelf->lat * (M_PI / 180);
        $oSelf->long = $oSelf->long * (M_PI / 180);
        
        return $oSelf;
    }
}

class Dijkstra {

    public $visited = array();
    public $distance = array();
    public $previousNode = array();
    public $startnode = null;
    public $map = array();
    public $infiniteDistance = 0;
    public $numberOfNodes = 0;
    public $bestPath = 0;
    public $matrixWidth = 0;

    public function __construct(&$ourMap, $infiniteDistance) {
        $this->infiniteDistance = $infiniteDistance;
        $this->map = &$ourMap;
        $this->numberOfNodes = count($ourMap);
        $this->bestPath = 0;
    }

    public function findShortestPath($start,$to = null) {
        $this->startnode = $start;
        foreach ($this->map as $node => $aLinks) {
            if ($node === $this->startnode) {
                $this->visited[$node] = true;
                $this->distance[$node] = 0;
            } else {
                $this->visited[$node] = false;
                $this->distance[$node] = isset($this->map[$this->startnode][$node]) 
                    ? $this->map[$this->startnode][$node] 
                    : $this->infiniteDistance;
            }
            $this->previousNode[$node] = $this->startnode;
        }
        
        $tries = 0;
        while (in_array(false,$this->visited,true) && $tries <= $this->numberOfNodes) {                    
            $this->bestPath = $this->findBestPath($this->distance, array_keys($this->visited, false, true));
            if ($to !== null && $this->bestPath == $to) {
                break;
            }
            $this->updateDistanceAndPrevious($this->bestPath);                    
            $this->visited[$this->bestPath] = true;
            $tries++;
        }
    }

    public function findBestPath($ourDistance, $ourNodesLeft) {
        $bestPath = $this->infiniteDistance;
        $bestNode = null;
        foreach ($ourNodesLeft as $nodeLeft) {
            if ($ourDistance[$nodeLeft] < $bestPath) {
                $bestPath = $ourDistance[$nodeLeft];
                $bestNode = $nodeLeft;
            }
        }
        return $bestNode;
    }

    public function updateDistanceAndPrevious($obp) {
        foreach ($this->map as $node => $aLinks) {
            if (
                (isset($this->map[$obp][$node])) &&  
                (!($this->map[$obp][$node] == $this->infiniteDistance) || ($this->map[$obp][$node] == 0 )) &&
                (($this->distance[$obp] + $this->map[$obp][$node]) < $this->distance[$node])
            ) {
                $this->distance[$node] = $this->distance[$obp] + $this->map[$obp][$node];
                $this->previousNode[$node] = $obp;
            }
        }
    }

    public function printMap(&$map) {
        $placeholder = ' %' . strlen($this->infiniteDistance) .'d';
        $foo = '';
        for($i=0,$im=count($map);$i<$im;$i++) {
            for ($k=0,$m=$im;$k<$m;$k++) {
                $foo.= sprintf($placeholder, isset($map[$i][$k]) ? $map[$i][$k] : $this->infiniteDistance);
            }
            $foo.= "\n";
        }
        return $foo;
    }

    public function getResults($to = null) {
        $ourShortestPath = array();
        $foo = '';
        for ($i = 0; $i < $this->numberOfNodes; $i++) {
            if ($to !== null && $to !== $i) {
                continue;
            }
            $ourShortestPath[$i] = array();
            $endNode = null;
            $currNode = $i;
            $ourShortestPath[$i][] = $i;
            while ($endNode === null || $endNode != $this->startnode) {
                $ourShortestPath[$i][] = $this->previousNode[$currNode];
                $endNode = $this->previousNode[$currNode];
                $currNode = $this->previousNode[$currNode];
            }
            $ourShortestPath[$i] = array_reverse($ourShortestPath[$i]);
            if ($to === null || $to === $i) {
                if ($this->distance[$i] >= $this->infiniteDistance) {
                    $foo .= sprintf("no route from %d to %d. \n", $this->startnode, $i);
                } else {
                    $foo .= sprintf(
                        '%d => %d = %d [%d]: (%s).'."\n" ,
                        $this->startnode,$i,$this->distance[$i],
                        count($ourShortestPath[$i]),
                        implode('-',$ourShortestPath[$i])
                    );
                }
                $foo .= str_repeat('-',20) . "\n";
                if ($to === $i) {
                    break;
                }
            }
        }
        return $foo;
    }
    
    public function getShortestPath($to = null) {
        $ourShortestPath = array();
        foreach ($this->map as $node => $aLinks) {
            if ($to !== null && $to !== $node) {
                continue;
            }
            $ourShortestPath[$node] = array();
            $endNode = null;
            $currNode = $node;
            $ourShortestPath[$node][] = $node;
            while ($endNode === null || $endNode != $this->startnode) {
                $ourShortestPath[$node][] = $this->previousNode[$currNode];
                $endNode = $this->previousNode[$currNode];
                $currNode = $this->previousNode[$currNode];
            }
            $ourShortestPath[$node] = array_reverse($ourShortestPath[$node]);
            if ($to === null || $to === $node) {
                if ($this->distance[$node] >= $this->infiniteDistance) {
                    $ourShortestPath[$node] = 'IMPOSSIBLE';
                    continue;
                }
                if ($to === $node) {
                    break;
                }
            }
        }
        
        if ($to === null) {
            return $ourShortestPath;
        }
        if (isset($ourShortestPath[$to])) {
            return $ourShortestPath[$to];
        }
        return 'IMPOSSIBLE';
    }
    
} // end class 

//Start and end point
fscanf(STDIN, "%s", $startPoint);
fscanf(STDIN, "%s", $endPoint);

//nb stops and list of stops
$oStations = new Stations();

//If same start than end, no need to parse all.
if ($startPoint === $endPoint) {
    echo $oStations->findStation($startPoint)->name . "\n";
    exit;
}

$aMap = [];
fscanf(STDIN, "%d", $M);
for ($i = 0; $i < $M; ++$i) {
    list($refA, $refB) = explode(' ', stream_get_line(STDIN, 256, "\n"));
    $oStationA = $oStations->findStation($refA);
    $oStationB = $oStations->findStation($refB);
    $aMap[$refA][$refB] = Stations::findDistance($oStationA, $oStationB);
    //Ensure there's 0 dist between to identical points
    $aMap[$refA][$refA] = 0;
    $aMap[$refB][$refB] = 0;
}

$oDijk = new Dijkstra($aMap, PHP_INT_MAX);

$oStartStop = $oStations->findStation($startPoint);
$oEndStop = $oStations->findStation($endPoint);

$oDijk->findShortestPath($startPoint, $endPoint);
$aAllRefStops = $oDijk->getShortestPath($endPoint);

if ($aAllRefStops === 'IMPOSSIBLE') {
    echo 'IMPOSSIBLE' . "\n";
    exit;
}

foreach ($aAllRefStops as $sRefStop) {
    echo $oStations->findStation($sRefStop)->name . "\n";
}
?>