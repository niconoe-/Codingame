#include <Foundation/Foundation.h>

int main(int argc, const char * argv[]) {
    int LX; // the X position of the light of power
    int LY; // the Y position of the light of power
    int TX; // Thor's starting X position
    int TY; // Thor's starting Y position
    scanf("%d%d%d%d", &LX, &LY, &TX, &TY);

    // game loop
    while (1) {
        int dY = (TY - LY);
        int dX = (TX - LX);
        NSMutableString *s = [NSMutableString stringWithString: @""];

        if (dY>0) {
            [s appendString: @"N"];
            TY--;
        } else if (dY<0) {
            [s appendString: @"S"];
            TY++;
        }

        if (dX>0) {
            [s appendString: @"W"];
            TX--;
        } else if (dX<0) {
            [s appendString: @"E"];
            TX++;
        }

        printf([@"%@\n" UTF8String], s);
    }
}