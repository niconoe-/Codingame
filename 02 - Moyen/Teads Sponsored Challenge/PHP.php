<?php
define('DEBUG', true);
function debug() {if (!DEBUG) return; foreach (func_get_args() as $sArgDebug) error_log(var_export($sArgDebug, true));}

$aPathes = array(); 
// le nombre n de relations au total.
fscanf(STDIN, "%d", $n);
for ($i = 0; $i < $n; ++$i) {
    $bAddedLink = false;
    //$xi = l'identifiant d'une personne liée à yi
    //$yi = l'identifiant d'une personne liée à xi
    fscanf(STDIN, "%d %d", $xi, $yi);
    
    foreach ($aPathes as $indexPath => &$aPath) {
        foreach ($aPath as $indexNode => $iNode) {
            if ($iNode === $xi) {
                if ($indexNode === count($aPath)-1) {
                    //If $iNode is the end of the path, just add the $yi
                    $aPath[] = $yi;
                    //Link is done, so let's get out of here and proceed to the new link
                    $bAddedLink = true;
                    break 1; //Don't check the node we just add
                } else {
                    //Otherwise, create a new path from the matching one until the $xi and add the $yi
                    $aNewPath = array_slice($aPath, 0, $indexNode+1);
                    $aNewPath[] = $yi;
                    $aPathes[] = $aNewPath;
                    //Link is done, so let's get out of here and proceed to the new link
                    $bAddedLink = true;
                    break 2; //Don't check the path we just add
                }
            }
            if ($iNode === $yi) {
                if ($indexNode === 0) {
                    //If $iNode is the start of the path, just add the $yi at the start
                    array_unshift($aPath, $xi);
                    //Link is done, so let's get out of here and proceed to the new link
                    $bAddedLink = true;
                    break 1; //Don't check the node we just add
                } else {
                    //Otherwise, create a new path from the matching one from the $yi and add the $xi before
                    $aNewPath = array_slice($aPath, $indexNode);
                    array_unshift($aNewPath, $xi);
                    $aPathes[] = $aNewPath;
                    //Link is done, so let's get out of here and proceed to the new link
                    $bAddedLink = true;
                    break 2; //Don't check the path we just add
                }
            }
        }
    } unset($aPath);
    //if $xi and $yi not found in any path, add a new path
    if (!$bAddedLink) {
        $aPathes[] = array($xi, $yi);
    }
    
    // if the two firsts nodes of a path matches the two lasts nodes of another path, merge them
    $aPathes = merge_pathes($aPathes);
}

function merge_pathes($aPathes)
{
    $aNewPathes = array();
    $aCopyPathes = $aPathes;
    $aFirsts = array();
    $aLasts = array();
    $aIndexToRemove = array();
    foreach ($aCopyPathes as $indexPath => $aPath) {
        $aFirsts[$indexPath] = array_slice($aPath, 0, 2);
        $aLasts[$indexPath] = array_slice($aPath, -2);
    }
    foreach ($aFirsts as $indexPathFirst => $aFirst) {
        foreach ($aLasts as $indexPathLast => $aLast) {
            if ($aFirst === $aLast) {
                $aIndexToRemove[] = $indexPathFirst;
                $aIndexToRemove[] = $indexPathLast;
                $aNewPathes[] = array_merge(
                    array_slice($aCopyPathes[$indexPathLast], 0, -2),
                    $aFirst,
                    array_slice($aCopyPathes[$indexPathFirst], 2)
                );
            }
        }
    }
    
    $aIndexToRemove = array_unique($aIndexToRemove);
    foreach ($aIndexToRemove as $index) {
        unset($aCopyPathes[$index]);
    }
    $aPathes = array_merge($aCopyPathes, $aNewPathes);
    return array_values($aPathes);
}

//At this start, $aPathes worth now all possible paths for this map.
//The minimal links to get through is the number of element we can found in the row at the start of all pathes or at the end of all
//pathes
debug($aPathes);

$aStartIntersect = call_user_func_array('array_intersect_assoc', $aPathes);
$aReversePath = array_map('array_reverse', $aPathes);
$aEndIntersect = call_user_func_array('array_intersect_assoc', $aReversePath);

$answer = max(count($aStartIntersect), count($aEndIntersect));
echo $answer . "\n";

// Write an action using echo(). DON'T FORGET THE TRAILING \n
// To debug (equivalent to var_dump): error_log(var_export($var, true));

//echo("1\n"); // Le nombre d'étapes minimum pour propager la publicité.
?>