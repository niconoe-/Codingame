#include <iostream>
#include <string>
#include <vector>
#include <algorithm>

using namespace std;

int main()
{
    int LX; // the X position of the light of power
    int LY; // the Y position of the light of power
    int TX; // Thor's starting X position
    int TY; // Thor's starting Y position
    cin >> LX >> LY >> TX >> TY; cin.ignore();

    // game loop
    while (1)
    {
        int dY = (TY - LY);
        int dX = (TX - LX);
        string s = "";

        if (dY>0) {
            s += "N";
            TY--;
        } else if (dY<0) {
            s += "S";
            TY++;
        }

        if (dX>0) {
            s += "W";
            TX--;
        } else if (dX<0) {
            s += "E";
            TX++;
        }

        cout << s << endl;
    }
}