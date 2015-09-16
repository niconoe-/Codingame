<?php
fscanf(STDIN, "%d", $N);
if ($N < 2) {
    echo 'false';
    exit;
}
for ($i=2; $i<$N; ++$i) {
    if (!($N%$i)) {
        echo 'false';
        exit;
    }
}
echo 'true';