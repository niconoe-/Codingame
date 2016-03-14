using System;
using System.Linq;
using System.IO;
using System.Text;
using System.Collections;
using System.Collections.Generic;

class Player
{
    static void Main(string[] args)
    {
        string[] inputs = Console.ReadLine().Split(' ');
        int LX = int.Parse(inputs[0]); // the X position of the light of power
        int LY = int.Parse(inputs[1]); // the Y position of the light of power
        int TX = int.Parse(inputs[2]); // Thor's starting X position
        int TY = int.Parse(inputs[3]); // Thor's starting Y position

        // game loop
        while (true)
        {
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

            Console.WriteLine(s);
        }
    }
}
