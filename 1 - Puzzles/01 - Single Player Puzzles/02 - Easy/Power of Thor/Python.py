import sys
import math

LX, LY, TX, TY = [int(i) for i in raw_input().split()]

# game loop
while 1:
    dY = int(TY - LY)
    dX = int(TX - LX)
    s = ""

    if (dY>0):
        s += "N"
        TY -= 1
    else:
        if (dY<0):
            s += "S"
            TY += 1

    if (dX>0):
        s += "W"
        TX -= 1
    else:
        if (dX<0):
            s += "E"
            TX += 1

    print s