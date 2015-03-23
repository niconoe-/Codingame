<?php
/**
 * Auto-generated code below aims at helping you parse
 * the standard input according to the problem statement.
 **/

$MESSAGE = stream_get_line(STDIN, 100, "\n");

// Write an action using echo(). DON'T FORGET THE TRAILING \n
// To debug (equivalent to var_dump): error_log(var_export($var, true));
$aMessage = str_split($MESSAGE);
$sBin = null;
foreach ($aMessage as $sLetter) {
    $sBin .= str_pad(decbin(ord($sLetter)), 7, '0', STR_PAD_LEFT);
}

$out = null;
$aBin = str_split($sBin);
error_log(var_export($aBin, true));
for($i = 0, $nb = count($aBin); $i<$nb; ++$i) {
    $cur = $aBin[$i];
    $firstBlock = ($cur ? '0 ' : '00 ');
    $secondBlock = '';
    do {
        $secondBlock .= '0';
        $i++;
    } while($i<$nb && $cur === $aBin[$i]);
    $out .= $firstBlock . $secondBlock . ' ';
    $i--;
}
$out = trim($out);

echo("$out\n");
?>