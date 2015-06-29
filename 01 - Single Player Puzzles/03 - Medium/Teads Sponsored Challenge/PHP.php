<?php
define('DEBUG', true);
function debug() {if (!DEBUG) return; foreach (func_get_args() as $sArgDebug) error_log(var_export($sArgDebug, true));}

class NodeStatus
{
    public $bEnded;
    public $iDepth;
    
    public function __construct($bEnded = false, $iDepth = 1)
    {
        $this->bEnded = $bEnded;
        $this->iDepth = $iDepth;
    }
    
    public function isEnd()
    {
        return $this->bEnded;
    }
    
    public static function findLongest($aNodesStatuses)
    {
        $iMax = 0;
        foreach ($aNodesStatuses as $oStatus) {
            if ($oStatus->iDepth > $iMax) {
                $iMax = $oStatus->iDepth;
            }
        }
        return $iMax;
    }
}

class Web
{
    public $aWeb;
    public $aCross;
    
    public function __construct()
    {
        fscanf(STDIN, "%d", $this->iNbNodes);
        for ($i=0; $i<$this->iNbNodes; ++$i) {
            fscanf(STDIN, "%d %d", $xi, $yi);
            (isset($this->aWeb[$xi])) ?: $this->aWeb[$xi] = [];
            (isset($this->aWeb[$yi])) ?: $this->aWeb[$yi] = [];
            $this->aWeb[$xi][$yi] = 1;
            $this->aWeb[$yi][$xi] = 1;
        }
        
        foreach (array_keys($this->aWeb) as $iNode) {
            ($this->isNodeCross($iNode)) ? $this->aCross[] = $iNode : null;
        }
    }
    
    public function trimWeb()
    {
        while (null !== ($iNode = array_shift($this->aCross))) {
            if (!isset($this->aWeb[$iNode])) {
                continue;
            }
            $this->trimCross($iNode);
            ($this->isNodeCross($iNode)) ? $this->aCross[] = $iNode : null;
        }
    }
    
    public function trimCross($iNode, $iParentNode = null, $depth = 0) 
    {
        $aVisited = [];
        $aChildren = $this->aWeb[$iNode];
        //If we came from a parent, the parent is visited and the link is not a child, but the parent.
        if (null !== $iParentNode) {
            unset($aChildren[$iParentNode]);
            $aVisited[$iParentNode] = new NodeStatus(false, $depth);
        }
        //If there's no more unvisited children, end because end of work for the branch of cross
        if (empty($aChildren)) {
            return new NodeStatus(true);
        }
        
        foreach (array_keys($aChildren) as $iChildNode) {
            $aVisited[$iChildNode] = $this->trimCross($iChildNode, $iNode, $depth+1);
        }
        
        //Look if there is an ended path shorter than 2 other paths, to remove it.
        foreach ($aVisited as $iChildNode => $oStatus) {
            if (count($aVisited) <= 2) {
                break;
            }
            
            //If path is not ended, it can't be removed.
            if (!$oStatus->isEnd()) {
                continue;
            }
            
            $iNbShorter = $this->compareStatusToVisited($oStatus, $aVisited, $iChildNode);
            if (2 === $iNbShorter) {
                unset($aVisited[$iChildNode]);
                $this->removeNode($iChildNode, $iNode);
            }
        }
        
        //Check if only known paths and max length, to return value.
        unset($aVisited[$iParentNode]);
        $iMaxDepth = NodeStatus::findLongest($aVisited);
        return new NodeStatus(true, $iMaxDepth+1);
    }
    
    public function isNodeCross($iNode) {
        return (count($this->aWeb[$iNode]) >= 3);
    }
    
    public function removeNode($iChildNode, $iNode) 
    {
        $aGrandChildren = $this->aWeb[$iChildNode];
        unset($aGrandChildren[$iNode]);
        unset($this->aWeb[$iChildNode]);
        unset($this->aWeb[$iNode][$iChildNode]);
        
        foreach (array_keys($aGrandChildren) as $iGrandChildNode) {
            $this->removeNode($iGrandChildNode, $iChildNode);
        }
    }
    
    public function compareStatusToVisited($oStatus, $aVisited, $iChildNode)
    {
        $iNbShorter = 0;
        foreach ($aVisited as $iSiblingNode => $oSiblingStatus) {
            if ($iChildNode === $iSiblingNode || $oStatus->iDepth > $oSiblingStatus->iDepth) {
                continue;
            }
            //If we find 2 shorters branch, no need to continue.
            if (2 === ++$iNbShorter) {
                break;
            }
        }
        return $iNbShorter;
    }
    
    public function answer()
    {
        $this->trimWeb();
        return floor(count($this->aWeb)/2);
    }
}

$oWeb = new Web();
echo $oWeb->answer() . "\n";
?>