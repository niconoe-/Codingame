<?php
/**
 * Auto-generated code below aims at helping you parse
 * the standard input according to the problem statement.
 **/

fscanf(STDIN, "%d %d %d %d %d %d %d %d",
    $nbFloors, // number of floors
    $width, // width of the area
    $nbRounds, // maximum number of rounds
    $exitFloor, // floor on which the exit is found
    $exitPos, // position of the exit on its floor
    $nbTotalClones, // number of generated clones
    $nbAdditionalElevators, // ignore (always zero)
    $nbElevators // number of elevators
);
$aElevators = array();
for ($i = 0; $i < $nbElevators; $i++)
{
    fscanf(STDIN, "%d %d",
        $elevatorFloor, // floor on which this elevator is found
        $elevatorPos // position of the elevator on its floor
    );
    $aElevators[$elevatorFloor] = $elevatorPos;
}

$HigherCloneFloor = 0;
// game loop
while (TRUE)
{
    fscanf(STDIN, "%d %d %s",
        $cloneFloor, // floor of the leading clone
        $clonePos, // position of the leading clone on its floor
        $direction // direction of the leading clone: LEFT or RIGHT
    );
    
    if ($HigherCloneFloor < $cloneFloor) {
        //Si on arrive au dernier étage
        $goal = ($cloneFloor === $exitFloor) ? $exitPos : $aElevators[$cloneFloor];
        
        if ($direction === "LEFT") {
            echo ($goal < $clonePos) ? "WAIT\n" : "BLOCK\n";
        } else {
            echo ($goal > $clonePos) ? "WAIT\n" : "BLOCK\n";
        }
        $HigherCloneFloor = $cloneFloor;
        continue;
    }

    // Write an action using echo(). DON'T FORGET THE TRAILING \n
    // To debug (equivalent to var_dump): error_log(var_export($var, true));
    
    echo ($clonePos === 0 || $clonePos === $width-1) ? "BLOCK\n" : "WAIT\n";
}
?>