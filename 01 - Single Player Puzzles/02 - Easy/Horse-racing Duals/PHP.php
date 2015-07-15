<?php
fscanf(STDIN, "%d", $N);
$aPi = [];
for ($i = 0; $i < $N; ++$i) {
    fscanf(STDIN, "%d", $aPi[]);
}

sort($aPi);
$minDiff = PHP_INT_MAX;
$prev = $aPi[0];
for ($i=1; $i<$N; ++$i) {
    $curr = $aPi[$i];
    $diff = $curr-$prev;
    if ($diff < $minDiff) {
        $minDiff = $diff;
    }
    $prev = $curr;
}

echo $minDiff . "\n";
?>