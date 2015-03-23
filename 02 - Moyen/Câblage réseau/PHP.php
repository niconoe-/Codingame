<?php
define('DEBUG', true);
function debug() {if (!DEBUG) return; foreach (func_get_args() as $sArgDebug) error_log(var_export($sArgDebug, true));}

fscanf(STDIN, "%d", $N);

$aMap = array();
$aXcoord = array();
$aYcoord = array();
for ($i = 0; $i < $N; ++$i) {
    fscanf(STDIN, "%d %d", $X, $Y);
    $aXcoord[] = $X;
    $aYcoord[] = $Y;
    if (!isset($aMap[$X])) {
        $aMap[$X] = array($Y);
    } else {
        $aMap[$X][] = $Y; 
    }
}

$aXcoord = array_unique($aXcoord);
sort($aXcoord);
sort($aYcoord);
$yMediane = $aYcoord[floor(count($aYcoord)/2)];

$longueurFil = 0;
$prevXPos = null;
foreach ($aXcoord as $xPos) {
    $yHouses = $aMap[$xPos];
    foreach ($yHouses as $iY) {
        $longueurFil += abs($iY - $yMediane);
        if ($prevXPos !== null) {
            $longueurFil += ($xPos - $prevXPos);
        }
        $prevXPos = $xPos;
    }
}

echo $longueurFil . "\n";

?>