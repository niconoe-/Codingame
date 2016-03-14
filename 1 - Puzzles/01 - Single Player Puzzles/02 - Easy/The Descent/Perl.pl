use strict;
use warnings;
#use diagnostics;
use 5.20.1;

select(STDOUT); $| = 1; # DO NOT REMOVE

# Auto-generated code below aims at helping you parse
# the standard input according to the problem statement.


# game loop
while (1) {
    chomp(my $tokens=<STDIN>);
    my ($space_x, $space_y) = split(/ /,$tokens);
    my $iHighest = -1;
    my $vHighest = 0;
    for my $i (0..7) {
        chomp(my $mountain_h = <STDIN>); # represents the height of one mountain, from 9 to 0. Mountain heights are provided from left to right.
        if ($vHighest <= $mountain_h) {
            $iHighest = $i;
            $vHighest = $mountain_h;
        }
    }

    if ($space_x == $iHighest) {
        print "FIRE\n";
    } else {
        print "HOLD\n"; # either:  FIRE (ship is firing its phase cannons) or HOLD (ship is not firing).
    }
}