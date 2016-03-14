<?php
/**
 * Auto-generated code below aims at helping you parse
 * the standard input according to the problem statement.
 **/

fscanf(STDIN, "%d", $n);
$maxValueLimit = pow(2, 31);
$minValueLimit = 0;

$vs = stream_get_line(STDIN, (strlen($maxValueLimit)+1)*$n+1, "\n");
$aValues = explode(' ', $vs);

$maxValue = null;
$minValue = null;
$maybeNewMaxValue = null;
$loose = 0;
$maxLoose = 0;
for ($i=0, $nb=count($aValues); $i<$nb-1; ++$i) {
    $iCurr = $aValues[$i];
    $iNext = $aValues[$i+1];
    
    //Courbe stagnante : pas de traitement
    if ($iCurr === $iNext) {
        continue;
    }
    
    //Courbe décroissante
    if ($iCurr > $iNext) {
        $maxValue = ($maxValue === null) ? $iCurr : $maxValue;
        $minValue = ($minValue === null) ? $iNext : min($minValue, $iNext);
        $loose = $minValue - $maxValue;
        if ($maybeNewMaxValue !== null) {
            $maxValue = $maybeNewMaxValue;
            $maybeNewMaxValue = null;
            $minValue = null;
        }
        $maxLoose = min($maxLoose, $loose);
        continue;
    }
    
    //Courbe croissante
    if ($iNext > $maxValue) {
        $maybeNewMaxValue = $iNext;
    }
}

if ($maxLoose === 0) {
    if ($minValue === null) {
        $minValue = 0;
    }
    
    if ($maxValue === null) {
        $maxValue = 0;
    }
    
    $maxLoose = $minValue - $maxValue;
}

// Write an action using echo(). DON'T FORGET THE TRAILING \n
// To debug (equivalent to var_dump): error_log(var_export($var, true));

echo("$maxLoose\n");
?>