<?php
define('DEBUG', true);
function debug() {if (!DEBUG) return; foreach (func_get_args() as $sArgDebug) error_log(var_export($sArgDebug, true));}

$aLinks = array(); 
$aY = array();
// le nombre n de relations au total.
fscanf(STDIN, "%d", $n);
for ($i = 0; $i < $n; ++$i) {
    //$x is related to $y
    fscanf(STDIN, "%d %d", $x, $y);
    $aY[] = $y;
    $aLinks[] = array($x, $y);
}

$cpt = 1;
$aY = array_unique($aY);
while(!empty($aLinks)) {
    $aCopyLinks = $aLinks;
    $aYisOrigin = array();
    foreach ($aY as $yElt) {
        foreach ($aLinks as $aLink) {
            if ($yElt === $aLink[0]) {
                $aYisOrigin[] = $yElt;
                break;
            }
        }
    }
    
    foreach ($aLinks as $iLink => $aLink) {
        //If the destination is an origin, unset the link
        if (!in_array($aLink[1], $aYisOrigin)) {
            unset($aCopyLinks[$iLink]);
        }
    }
    
    $cpt++;
    $aLinks = $aCopyLinks;
    $aY = $aYisOrigin;
}

echo $cpt . "\n";
?>