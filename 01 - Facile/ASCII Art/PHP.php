<?php
/**
 * Auto-generated code below aims at helping you parse
 * the standard input according to the problem statement.
 **/

fscanf(STDIN, "%d",
    $L
);
fscanf(STDIN, "%d",
    $H
);
$T = stream_get_line(STDIN, 256, "\n");
$aChars = range('A', 'Z');
for ($i = 0; $i < $H; $i++)
{
    $ROW = stream_get_line(STDIN, 1024, "\n");
    $aLetters[] = str_split($ROW, $L);
}

$charT = str_split($T);
$answer = null;
for ($i = 0; $i < $H; $i++)
{
    foreach ($charT as $char) {
        $iLetters = array_search(strtoupper($char), $aChars);
        if ($iLetters === false) {
            $answer .= end($aLetters[$i]);
        } else {
            $answer .= $aLetters[$i][$iLetters];
        }
    }
    $answer .= "\n";
}


// Write an action using echo(). DON'T FORGET THE TRAILING \n
// To debug (equivalent to var_dump): error_log(var_export($var, true));

echo("$answer\n");
?>