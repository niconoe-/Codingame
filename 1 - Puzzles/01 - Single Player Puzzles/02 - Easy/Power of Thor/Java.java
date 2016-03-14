import java.util.*;
import java.io.*;
import java.math.*;

class Player {

    public static void main(String args[]) {
        Scanner in = new Scanner(System.in);
        int LX = in.nextInt(); // the X position of the light of power
        int LY = in.nextInt(); // the Y position of the light of power
        int TX = in.nextInt(); // Thor's starting X position
        int TY = in.nextInt(); // Thor's starting Y position

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

            System.out.println(s);
        }
    }
}