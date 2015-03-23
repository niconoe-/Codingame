<?php
define('DEBUG', true);
function debug() {if (!DEBUG) return; foreach (func_get_args() as $sArgDebug) error_log(var_export($sArgDebug, true));}
function e($a){echo implode(' ', $a) . "\n";}

//$N the total number of nodes in the level, including the gateways
//$L the number of links
//$E the number of exit gateways
fscanf(STDIN, "%d %d %d", $N, $L, $E);
$aTreeLink = [];
$links = array();
for ($i = 0; $i < $L; ++$i) {
    // N1 and N2 defines a link between these nodes
    fscanf(STDIN, "%d %d", $N1, $N2);
    $links[] = array($N1, $N2);
    isset($aTreeLink[$N1]) ?: $aTreeLink[$N1] = array();
    isset($aTreeLink[$N2]) ?: $aTreeLink[$N2] = array();
    $aTreeLink[$N1][] = $N2;
    $aTreeLink[$N2][] = $N1;
}
$aEIChildren = array();
$aSevers = array();
for ($i = 0; $i < $E; ++$i) {
    // the index of a gateway node
    fscanf(STDIN, "%d", $EI);
    foreach ($links as $aNodes) {
        list($node1, $node2) = $aNodes;
        if ($EI === $node1 || $EI === $node2) {
            $aSevers[] = array($node1, $node2);
        }
        if ($EI === $node1) {
            $aEIChildren[$EI][] = $node2;
        } elseif ($EI === $node2) {
            $aEIChildren[$EI][] = $node1;
        }
    }
}

$aGoingOutsideChildren = [];
foreach ($aEIChildren as $iGateway => $aChildren) {
    foreach ($aChildren as $iChild) {
        foreach ($aTreeLink[$iChild] as $iChildLinkedNode) {
            if (!in_array($iChildLinkedNode, $aChildren) && $iChildLinkedNode !== $iGateway) {
                isset($aGoingOutsideChildren[$iGateway]) ?: $aGoingOutsideChildren[$iGateway] = array();
                $aGoingOutsideChildren[$iGateway][] = $iChild;
                break;
            }
        }
    }
}
$aCriticalLinks = [];
foreach ($aGoingOutsideChildren as $iGateway => $aCriticalGWNodes) {
    foreach ($aCriticalGWNodes as $iCritNode) {
        $aIntersectChildren = array_intersect($aTreeLink[$iCritNode], $aEIChildren[$iGateway]);
        foreach ($aIntersectChildren as $iIntersectChild) {
            if (!in_array($iIntersectChild, $aCriticalGWNodes)) {
                $aCriticalLinks[] = array($iIntersectChild, $iCritNode);
            }
        }
    }
}

// game loop
while (TRUE) {
    // The index of the node on which the Skynet agent is positioned this turn
    fscanf(STDIN, "%d", $SI);
    
    $close = false;
    foreach ($aSevers as $i=>$aNodes) {
        if (in_array($SI, $aNodes)) {
            $close = true;
            e($aNodes);
            unset($aSevers[$i]);
            break;
        }
    }
    
    if ($close) {
        continue;
    }
    if (empty($aCriticalLinks)) {
        $aNodes = array_shift($aSevers);
        e($aNodes);
        continue;
    }
    
    $aNodes = array_shift($aCriticalLinks);
    e($aNodes);
}
?>