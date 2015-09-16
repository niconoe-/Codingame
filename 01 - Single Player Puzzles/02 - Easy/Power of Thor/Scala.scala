import math._
import scala.util._

object Player extends App {
    var Array(lx, ly, tx, ty) = for(i <- readLine split " ") yield i.toInt

    // game loop
    while(true) {
       var dy = (ty - ly)
       var dx = (tx - lx)
       var s = ""

       if (dy>0) {
           s += "N"
           ty = ty - 1
       } else if (dy<0) {
           s += "S"
           ty = ty + 1
       }

       if (dx>0) {
           s += "W"
           tx= tx - 1
       } else if (dx<0) {
           s += "E"
           tx= tx + 1
       }

       println(s)
    }
}