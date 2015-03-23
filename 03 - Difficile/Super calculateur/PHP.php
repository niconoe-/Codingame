<?php
define('DEBUG', true);
function debug() {if (!DEBUG) return; foreach (func_get_args() as $sArgDebug) error_log(var_export($sArgDebug, true));}

$aDays = array();
fscanf(STDIN, "%d", $N);
for ($i = 0; $i < $N; ++$i) {
    fscanf(STDIN, "%d %d", $J, $D);
    $aDays[$i] = array($J, $D, $J+$D-1);
}

usort($aDays, function($a, $b) {return ($a[2] === $b[2] ? 0 : ($a[2] < $b[2] ? -1 : 1));});
for ($i = 0, $nbCalc = 0, $startingDay = -1; $i < $N; ++$i) {
    ($startingDay < $aDays[$i][0]) ? ($startingDay = $aDays[$i][2]) && ++$nbCalc : null;
}

echo $nbCalc . "\n";
?>