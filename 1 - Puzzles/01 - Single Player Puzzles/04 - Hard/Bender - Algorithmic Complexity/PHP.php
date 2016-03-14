<?php
define('DEBUG', true);
function debug() {if (!DEBUG) return; foreach (func_get_args() as $sArgDebug) error_log(var_export($sArgDebug, true));}

fscanf(STDIN, "%d", $N);
$aNum = [];
for ($i = 0; $i < $N; ++$i) {
    fscanf(STDIN, "%d %d", $num, $t);
    $aNum[$num] = $t;
}

//Try to find O(1)
if(!isO1($aNum, $N)){echo '';}else{ echo 'O(1)' . "\n"; exit;}
if(!isOlogN($aNum, $N)){echo '';}else{ echo 'O(log n)' . "\n"; exit;}
if(!isON($aNum, $N)){echo '';}else{ echo 'O(n)' . "\n"; exit;}
if(!isONlogN($aNum, $N)){echo '';}else{ echo 'O(n log n)' . "\n"; exit;}
if(!isON2($aNum, $N)){echo '';}else{ echo 'O(n^2)' . "\n"; exit;}
//Too complicated to find O(n^2 log n). It returns false to continue. If no complexity found, it was it.
if(!isON2logN($aNum, $N)){echo '';}else{ echo 'O(n^2 log n)' . "\n"; exit;}
if(!isON3($aNum, $N)){echo '';}else{ echo 'O(n^3)' . "\n"; exit;}
if(!isO2N($aNum, $N)){echo '';}else{ echo 'O(2^n)' . "\n"; exit;}

echo 'O(n^2 log n)' . "\n";

function isO1($aNum, $N)
{
    $avg = array_sum($aNum) / $N;
    $min = 0.5*$avg;
    $max = 1.5*$avg;
    $bNot = false;
    foreach ($aNum as $t) {
        if ($t >= $min && $t <= $max) {
            continue;
        }
        $bNot = true;
        break;
    }
    
    return !$bNot;
}

function isOlogN($aNum, $N)
{
    $nbUnexpected = 0;
    $previousDiv = null;
    $aEllipse = [];
    $aTValues = array_values($aNum);
    for ($i=0; $i<$N-1; ++$i) {
        $aEllipse[] = $aTValues[$i+1] - $aTValues[$i];
    }
    
    //If the 10 last % of elements have bearly 50% of positive ellipse and 50% of negative ellipse, we're in O(log n)
    $nbLastElement = floor($N/10);
    $aLastElts = array_slice($aEllipse, $N-$nbLastElement);
    
    $nbPos = $nbNeg = 0;
    foreach ($aLastElts as $diff) {
        if ($diff < 0) {
            $nbPos++;
        } else {
            $nbNeg++;
        }
    }
    
    $min = 0.4*($nbLastElement);
    $max = 0.6*($nbLastElement);
    if ($min <= $nbPos && $max >= $nbPos && $min <= $nbNeg && $max >= $nbNeg) {
        return true;
    }
    return false;
}


function isON($aNum, $N)
{
    $aDiv = [];
    $bUnexpected = 0;
    foreach ($aNum as $num => $t) {
        $aDiv[] = floor($t/$num);
    }
    $avg = array_sum($aDiv) / $N;
    $min = 0.9*$avg;
    $max = 1.1*$avg;
    foreach ($aDiv as $div) {
        if ($div < $min || $div > $max) {
            $bUnexpected++;
        }
    }
    
    if ($N/10 < $bUnexpected) {
        return false;
    }
    return true;
}

function isONlogN($aNum, $N)
{
    $aDiv = [];
    $bUnexpected = 0;
    foreach ($aNum as $num => $t) {
        $aDiv[] = $t/$num;
    }
    
    $aEllipse = [];
    for ($i=0; $i<$N-1; ++$i) {
        $aEllipse[] = $aDiv[$i+1] - $aDiv[$i];
    }
    
    //If the 10 last % of elements have bearly 50% of positive ellipse and 50% of negative ellipse, we're in O(log n)
    $nbLastElement = floor($N/10);
    $aLastElts = array_slice($aEllipse, $N-$nbLastElement);
    foreach ($aLastElts as $diff) {
        if ($diff >= -0.5 && $diff <= 0.5) {
            continue;
        }
        return false;
    }
    return true;
}

function isON2($aNum, $N)
{
    $aDiv = [];
    $bUnexpected = 0;
    foreach ($aNum as $num => $t) {
        $aDiv[] = $t/($num*$num);
    }
    $avg = array_sum($aDiv) / $N;
    $min = 0.9*$avg;
    $max = 1.1*$avg;
    foreach ($aDiv as $div) {
        if ($div < $min || $div > $max) {
            $bUnexpected++;
        }
    }
    
    if ($N/10 < $bUnexpected) {
        return false;
    }
    return true;
}

function isON2logN($aNum, $N)
{
    return false;
}

function isON3($aNum, $N)
{
    $aDiv = [];
    $bUnexpected = 0;
    foreach ($aNum as $num => $t) {
        $aDiv[] = $t/($num*$num*$num);
    }
    
    
    //Ignore the first 10% of value.
    $nbIgnore = floor($N/10);
    $aDiv = array_slice($aDiv, $nbIgnore);
    
    $avg = array_sum($aDiv) / count($aDiv);
    $min = 0.7*$avg;
    $max = 1.3*$avg;
    foreach ($aDiv as $div) {
        if ($div < $min || $div > $max) {
            $bUnexpected++;
        }
    }
    
    if ($nbIgnore < $bUnexpected) {
        return false;
    }
    return true;
}

function isO2N($aNum, $N)
{
    $aDiv = [];
    foreach ($aNum as $num => $t) {
        $aDiv[] = log10($t);
    }
    
    $nbUnexpected = 0;
    $aEllipse = [];
    $aTValues = array_values($aNum);
    for ($i=0; $i<$N-1; ++$i) {
        $aEllipse[] = $aDiv[$i+1] - $aDiv[$i];
    }
    
    $avg = array_sum($aEllipse) / ($N-1);
    $min = 0.8*$avg;
    $max = 1.2*$avg;
    foreach ($aEllipse as $diff) {
        if ($diff < 0) {
            continue;
        }
        if ($diff < $min || $diff > $diff) {
            $nbUnexpected++;
        }
    }
    
    if ($N/10 < $nbUnexpected) {
        return false;
    }
    return true;
}
?>