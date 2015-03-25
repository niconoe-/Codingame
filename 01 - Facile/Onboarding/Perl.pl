select(STDOUT); $| = 1; # DO NOT REMOVE
while (1) {
    chomp($enemy1 = <STDIN>);
    chomp($dist1 = <STDIN>);
    chomp($enemy2 = <STDIN>);
    chomp($dist2 = <STDIN>);
    
    if ($dist1 < $dist2) {
        print $enemy1;
        print "\n";
    } else {
        print $enemy2;
        print "\n";
    }
}