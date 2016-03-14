<?php

$aOpenExpected = ['(', '[', '{'];
$aCloseExpected = [')', ']', '}'];

$aOpened = [];

fscanf(STDIN, "%s", $expression);
for ($i=0, $l=strlen($expression); $i<$l; ++$i) {
    $s = $expression[$i];
    if (!in_array($s, $aOpenExpected) && !in_array($s, $aCloseExpected)) {
        continue;
    }
    if (in_array($s, $aOpenExpected)) {
        $aOpened[] = $s;
        continue;
    }

    //Character is a closing bracket.
    if (empty($aOpened)) {
        echo 'false';
        exit;
    }
    $sLastOpened = end($aOpened);
    if (array_search($s, $aCloseExpected) !== array_search($sLastOpened, $aOpenExpected)) {
        echo 'false';
        exit;
    }
    array_pop($aOpened);
}

if (empty($aOpened)) {
    echo 'true';
} else {
    echo 'false';
}
