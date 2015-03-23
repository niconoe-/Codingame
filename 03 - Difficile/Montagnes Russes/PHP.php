<?php
define('DEBUG', true);
function debug() {if (!DEBUG) return; foreach (func_get_args() as $sArgDebug) error_log(var_export($sArgDebug, true));}


//$L = nb limit place
//$C = nb run in a day
//$N = nb groups of people
$aGroups = [];
fscanf(STDIN, "%d %d %d", $L, $C, $N);
for ($i = 0; $i < $N; ++$i) {
    fscanf(STDIN, "%d", $aGroups[]);
}


//Optimisation: if all people of all groups can go in one time, result is nbPeople * nb run in a day
if (array_sum($aGroups) <= $L) {
    echo (array_sum($aGroups) * $C) . "\n";
    exit;
}

$bFoundRepetition = false;

//Store nbDirham for each turn
$nbDirham = [];
$aHistoGroups = [$aGroups];

for ($i=1; $i<=$C; ++$i) {
    $currentL = 0;
    $bReachMax = false;
    $aGroupsInTrain = [];
    
    while (!$bReachMax && !empty($aGroups)) {
        //Remove the next group
        $nbPeople = array_shift($aGroups);
        //If this group is too large, put it back on first
        if ($L < ($currentL + $nbPeople)) {
            array_unshift($aGroups, $nbPeople);
            $bReachMax = true;
        } else {
            //Otherwise, add the group into the train
            $currentL += $nbPeople;
            $aGroupsInTrain[] = $nbPeople;
        }
    }
    //Here we go for a run!
    $nbDirham[$i] = array_sum($aGroupsInTrain);
    
    //Replace group in the queue
    $aGroups = array_merge($aGroups, $aGroupsInTrain);
    
    //If we found a repetition in the group list, the number of dirhams won for this repetition will still be the same.
    if (!$bFoundRepetition && false !== ($iFound = array_search($aGroups, $aHistoGroups))) {
        $iSameNbDirham = $i - $iFound;
        $aSameNbDirham = array_slice($nbDirham, $iFound, $iSameNbDirham);
        
        //From here, we made $i run on the $C required.
        $nbRoundsLeft = $C - $i;
        //How many sequences can we do with this frequency
        $nbSequences = floor($nbRoundsLeft / $iSameNbDirham);
        //If some sequences are left, do them, otherwise, we already did the last sequence so don't calculate.
        if ($nbSequences > 0) {
            
            //Replace $i at the end of the repeating sequence
            $i += ($iSameNbDirham * $nbSequences);
            
            //Number of dirhams win after repeating the sequence $nbSequences times
            $nbDirham[$i] = (array_sum($aSameNbDirham) * $nbSequences);
            $bFoundRepetition = true;
        }
    } elseif (!$bFoundRepetition) {
        $aHistoGroups[] = $aGroups;
    }
}

$totalWin = array_sum($nbDirham);
echo $totalWin . "\n";
?>