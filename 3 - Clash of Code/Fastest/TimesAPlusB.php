<?php
fscanf(STDIN, "%d %d",
    $a,
    $b
);
fscanf(STDIN, "%d",
    $n
);
for ($i = 0; $i < $n; $i++)
{
    fscanf(STDIN, "%d",
        $x
    );

    echo $x*$a + $b . "\n";
}