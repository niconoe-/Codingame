import 'dart:io';
import 'dart:math';

void main() {
    List inputs;

    // game loop
    while (true) {
        inputs = stdin.readLineSync().split(' ');
        int spaceX = int.parse(inputs[0]);
        int spaceY = int.parse(inputs[1]);
        int iHighest = -1;
        int vHighest = 0;
        for (int i = 0; i < 8; i++) {
            int mountainH = int.parse(stdin.readLineSync()); // represents the height of one mountain, from 9 to 0. Mountain heights are provided from left to right.
            if (vHighest <= mountainH) {
                iHighest = i;
                vHighest = mountainH;
            }
        }

        if (spaceX == iHighest) {
            print('FIRE');
            continue;
        }
        print('HOLD');
    }
}
