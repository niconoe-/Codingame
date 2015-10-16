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

        // game loop
        while (true)
        {
            string[] inputs = Console.ReadLine().Split(' ');
            int spaceX = int.Parse(inputs[0]);
            int spaceY = int.Parse(inputs[1]);
            int iHighest = -1;
            int vHighest = 0;

            for (int i = 0; i < 8; i++)
            {
                // represents the height of one mountain, from 9 to 0. Mountain heights are provided from left to right.
                int mountainH = int.Parse(Console.ReadLine());
                if (vHighest <= mountainH) {
                    iHighest = i;
                    vHighest = mountainH;
                }
            }

            if (spaceX == iHighest) {
                Console.WriteLine("FIRE");
                continue;
            }

            Console.WriteLine("HOLD");
        }
    }
}
