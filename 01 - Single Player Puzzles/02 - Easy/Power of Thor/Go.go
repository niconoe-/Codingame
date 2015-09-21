package main

import "fmt"
//import "os"

func main() {
    // lightX: the X position of the light of power
    // lightY: the Y position of the light of power
    // initialTX: Thor's starting X position
    // initialTY: Thor's starting Y position
    var LX, LY, TX, TY int
    fmt.Scan(&LX, &LY, &TX, &TY)
    var dY, dX int
    var s string

    for {
        dY = TY - LY
        dX = TX - LX
        s = ""

        if (dY>0) {
            s += "N"
            TY--
        } else if (dY<0) {
            s += "S"
            TY++
        }

        if (dX>0) {
            s += "W"
            TX--
        } else if (dX<0) {
            s += "E"
            TX++
        }
        fmt.Println(s)
    }
}