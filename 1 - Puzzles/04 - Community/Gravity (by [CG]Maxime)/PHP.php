<?php

fscanf(STDIN, "%d %d", $width, $height);
$aCols = array_fill(0, $width, 0);
for ($i = 0; $i < $height; $i++) {
    fscanf(STDIN, "%s", $line);
    for ($j = 0; $j < $width; ++$j) {
        if ($line[$j] === '#') {
            $aCols[$j]++;
        }
    }
}

$sResult = '';
for ($i = 0; $i < $height; $i++) {
    for ($j=$width-1; $j>=0; --$j) {
        if ($aCols[$j]>0) {
            $sResult .= '#';
            $aCols[$j]--;
        } else {
            $sResult .= '.';
        }
    }
    $sResult .= PHP_EOL;
}
echo strrev(trim($sResult));