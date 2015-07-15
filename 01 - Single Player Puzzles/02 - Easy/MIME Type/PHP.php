<?php
/**
 * Auto-generated code below aims at helping you parse
 * the standard input according to the problem statement.
 **/

fscanf(STDIN, "%d",
    $N // Number of elements which make up the association table.
);
fscanf(STDIN, "%d",
    $Q // Number Q of file names to be analyzed.
);
$aMimes = array();
for ($i = 0; $i < $N; $i++)
{
    fscanf(STDIN, "%s %s",
        $EXT, // file extension
        $MT // MIME type.
    );
    if (strlen($EXT) > 10 || strlen($MT) > 50) {
        continue;
    }
    $aMimes[strtolower($EXT)] = $MT;
}
$out = null;
for ($i = 0; $i < $Q; $i++)
{
    $FNAME = stream_get_line(STDIN, 1024, "\n"); // One file name per line.
    $extension = strtolower(pathinfo($FNAME, PATHINFO_EXTENSION));
    if (isset($aMimes[$extension])) {
        $out .= $aMimes[$extension] . "\n";
    } else {
        $out .= "UNKNOWN\n";
    }
}



// Write an action using echo(). DON'T FORGET THE TRAILING \n
// To debug (equivalent to var_dump): error_log(var_export($var, true));

echo("$out\n"); // For each of the Q filenames, display on a line the corresponding MIME type. If there is no corresponding type, then display UNKNOWN.
?>