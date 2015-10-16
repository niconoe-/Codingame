import math._
import scala.util._

/**
 * Auto-generated code below aims at helping you parse
 * the standard input according to the problem statement.
 **/
object Player extends App {

    // game loop
    while(true) {
        val Array(spacex, spacey) = for(i <- readLine split " ") yield i.toInt
        var ihighest = -1
        var vhighest = 0
        for(i <- 0 until 8) {
            var mountainh = readInt // represents the height of one mountain, from 9 to 0. Mountain heights are provided from left to right.
            if (vhighest <= mountainh) {
                ihighest = i
                vhighest = mountainh
            }
        }

        if (spacex == ihighest) {
           println("FIRE")
        } else {
            println("HOLD")
        }
    }
}