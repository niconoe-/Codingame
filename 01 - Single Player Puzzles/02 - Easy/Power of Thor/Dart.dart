import 'dart:io';
import 'dart:math';

void main() {
    List inputs;
    inputs = stdin.readLineSync().split(' ');
    int LX = int.parse(inputs[0]); // the X position of the light of power
    int LY = int.parse(inputs[1]); // the Y position of the light of power
    int TX = int.parse(inputs[2]); // Thor's starting X position
    int TY = int.parse(inputs[3]); // Thor's starting Y position

    // game loop
    while (true) {
        int dY = (TY - LY);
        int dX = (TX - LX);
        String s = "";

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

        print(s);
    }
}
