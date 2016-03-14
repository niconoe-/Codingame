<?php
define('DEBUG', true);
function debug() {if (!DEBUG) return; foreach (func_get_args() as $sArgDebug) error_log(var_export($sArgDebug, true));}

fscanf(STDIN, "%d", $R);
fscanf(STDIN, "%d", $L);

$iCurr = 1;
$aElements = array($R);
while ($iCurr < $L) {
    $aNewElements = array();
    $iElt = 0;
    while (isset($aElements[$iElt])) {
        $firstEltCons = $aElements[$iElt];
        $nbEltCons = 0;
        do {
            $nbEltCons++;
            $iElt++;
        } while (isset($aElements[$iElt]) && $firstEltCons === $aElements[$iElt]);
        $aNewElements[] = $nbEltCons;
        $aNewElements[] = $firstEltCons;
    }
    $iCurr++;
    $aElements = $aNewElements;
}

echo implode(' ', $aElements) . "\n";
?>