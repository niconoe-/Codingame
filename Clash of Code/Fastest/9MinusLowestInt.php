<?
fscanf(STDIN,"%s",$N);
for($i=0;$i<strlen($N);++$i) {
    ($N[$i]<5)?:$N[$i]=9-$N[$i];
} echo $N;