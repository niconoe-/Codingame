#include <stdlib.h>
#include <stdio.h>
#include <string.h>

int main()
{
    int LX; // the X position of the light of power
    int LY; // the Y position of the light of power
    int TX; // Thor's starting X position
    int TY; // Thor's starting Y position
    scanf("%d%d%d%d", &LX, &LY, &TX, &TY);

    // game loop
    while (1) {
        int dY = (TY - LY);
        int dX = (TX - LX);
        char s[5] = "";

        if (dY>0) {
            strcat(s, "N");
            TY--;
        } else if (dY<0) {
            strcat(s, "S");
            TY++;
        }

        if (dX>0) {
            strcat(s, "W");
            TX--;
        } else if (dX<0) {
            strcat(s, "E");
            TX++;
        }

        printf("%s\n", s);
    }

    return 0;
}