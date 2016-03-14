<?php
/**
 * Auto-generated code below aims at helping you parse
 * the standard input according to the problem statement.
 **/

fscanf(STDIN, "%d",
    $R // the length of the road before the gap.
);
fscanf(STDIN, "%d",
    $G // the length of the gap.
);
fscanf(STDIN, "%d",
    $L // the length of the landing platform.
);

// game loop
while (TRUE)
{
    fscanf(STDIN, "%d",
        $S // the motorbike's speed.
    );
    fscanf(STDIN, "%d",
        $X // the position on the road of the motorbike.
    );
    
    if ($S > $G+1) {
        echo "SLOW\n";
        continue;
    }
    
    if ($X < $R-1) {
        if ($S <= $G) {
            echo "SPEED\n";
            continue;
        }
        echo "WAIT\n";
        continue;
    } 
    if($X === $R-1) {
        echo "JUMP\n";
        continue;
    }
    echo "SLOW\n";
    
    

    // Write an action using echo(). DON'T FORGET THE TRAILING \n
    // To debug (equivalent to var_dump): error_log(var_export($var, true));

   // echo("SPEED\n"); // A single line containing one of 4 keywords: SPEED, SLOW, JUMP, WAIT.
}
?>