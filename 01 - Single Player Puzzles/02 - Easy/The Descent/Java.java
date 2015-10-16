import java.util.*;
import java.io.*;
import java.math.*;

class Player {

    public static void main(String args[]) {
        Scanner in = new Scanner(System.in);

        // game loop
        while (true) {
            int spaceX = in.nextInt();
            int spaceY = in.nextInt();

            int iHighest = -1;
            int vHighest = 0;

            for (int i = 0; i < 8; i++) {
                int mountainH = in.nextInt();
                if (vHighest <= mountainH) {
                    iHighest = i;
                    vHighest = mountainH;
                }
            }

            if (spaceX == iHighest) {
                System.out.println("FIRE");
            } else {
                System.out.println("HOLD");
            }
        }
    }
}
