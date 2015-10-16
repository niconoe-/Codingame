#include <iostream>
#include <string>
#include <vector>
#include <algorithm>

using namespace std;

/**
 * Auto-generated code below aims at helping you parse
 * the standard input according to the problem statement.
 **/
int main()
{

    // game loop
    while (1) {
        int spaceX;
        int spaceY;
        cin >> spaceX >> spaceY; cin.ignore();
        int iHighest = -1;
        int vHighest = 0;
        for (int i = 0; i < 8; i++) {
            int mountainH; // represents the height of one mountain, from 9 to 0. Mountain heights are provided from left to right.
            cin >> mountainH; cin.ignore();

            if (vHighest <= mountainH) {
                iHighest = i;
                vHighest = mountainH;
            }
        }

        if (spaceX == iHighest) {
            cout << "FIRE" << endl;
            continue;
        }
        cout << "HOLD" << endl;
    }
}