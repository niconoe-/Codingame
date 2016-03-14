<?php
define('DEBUG', true);
function debug() {if (!DEBUG) return; foreach (func_get_args() as $sArgDebug) error_log(var_export($sArgDebug, true));}

$nbNum = 0;
$aTels = array();
fscanf(STDIN, "%d", $N);
for ($i = 0; $i < $N; ++$i) {
    fscanf(STDIN, "%s", $phone);
    $aDigits = str_split($phone);
    $array =& $aTels;
    foreach ($aDigits as $sDigit) {
        if (!isset($array[$sDigit])) {
            $array[$sDigit] = array();
            $nbNum++;
        }
        $array =& $array[$sDigit];
    }
}

echo $nbNum . "\n";
?>