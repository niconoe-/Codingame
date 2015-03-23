<?php
define('DEBUG', true);
function debug() {if (!DEBUG) return; foreach (func_get_args() as $sArgDebug) error_log(var_export($sArgDebug, true));}

function mergeString($a, $b)
{
    $suffix_len = strlen($b); // assume $b matches the suffix of $a

    // compare suffix of $a with prefix of $b
    while ($suffix_len && substr_compare($a, $b, -$suffix_len, $suffix_len) != 0) {
        --$suffix_len; // remove one character off the end
    }
    // test whether we have a match
    return $suffix_len ? $a . substr($b, $suffix_len) : $a;
}

function findMerge(&$aSub, $sCurr)
{
    //Try to find the smaller new string for each elements
    $sNext = array_shift($aSub);
    $sNewA = mergeString($sCurr, $sNext);
    $sNewB = mergeString($sNext, $sCurr);
    if ($sCurr !== $sNewA && $sNext !== $sNewB) {
        return (strlen($sNewA) < strlen($sNewB) ? $sNewA : $sNewB);
    }
    if ($sCurr !== $sNewA) {
        return $sNewA;
    } 
    if ($sNext !== $sNewB) {
        return $sNewB;
    } 
    
    //No matches from back and forth... if no more possibilities, concat, otherwise, try with another subsequence
    if (empty($aSub)) {
        $sNew = $sCurr . $sNext;
        return $sNew;
    } else {
        $aSub[] = $sCurr; //Save $sCurr again and go to next.
        return findMerge($aSub, $sNext);
    }
}

$aSub = array();
fscanf(STDIN, "%d", $N); // N is smaller than 6 so we can make hard loops
for ($i = 0; $i < $N; ++$i) {
    fscanf(STDIN, "%s", $subseq);
    $bDontAdd = false;
    foreach ($aSub as $index => $sSub) {
        //If the whole string is find in any of the array, don't add it because the longer contains the smaller
        if (false !== strpos($sSub, $subseq)) {
            $bDontAdd = true;
        }
        //If any of the array is find in the whole string, remove it because the longer contains the smaller
        if (false !== strpos($subseq, $sSub)) {
            unset($aSub[$index]);
        }
    }
    if (!$bDontAdd) {
        $aSub[] = $subseq;
    }   
}

while (count($aSub) > 1) {
    $sCurr = array_shift($aSub);
    $sNew = findMerge($aSub, $sCurr);
    array_unshift($aSub, $sNew);
}

$sSub = reset($aSub);
echo strlen($sSub) . "\n";
?>