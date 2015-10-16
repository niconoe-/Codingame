import sys
import math

# Auto-generated code below aims at helping you parse
# the standard input according to the problem statement.


# game loop
while 1:
    space_x, space_y = [int(i) for i in input().split()]
    i_highest = -1
    v_highest = 0
    for i in range(8):
        mountain_h = int(input())
        if (v_highest <= mountain_h):
            i_highest = i
            v_highest = mountain_h

    if (space_x == i_highest):
       print ("FIRE")
       continue

    print("HOLD")
