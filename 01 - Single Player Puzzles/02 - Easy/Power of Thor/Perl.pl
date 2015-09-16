use strict;
use warnings;
#use diagnostics;
use 5.20.1;

select(STDOUT); $| = 1; # DO NOT REMOVE

chomp(my $tokens=<STDIN>);
my ($LX, $LY, $TX, $TY) = split(/ /,$tokens);

# game loop
while (1) {
    my $dY = $TY-$LY;
    my $dX = $TX-$LX;
    my $s = "";

    if ($dY>0) {
        $s.="N";
        $TY--;
    } else {
        if ($dY<0) {
            $s.="S";
            $TY++;
        }
    }

    if ($dX>0) {
        $s.="W";
        $TX--;
    } else {
        if ($dX<0) {
            $s.="E";
            $TX++;
        }
    }

    print $s . "\n";
}
