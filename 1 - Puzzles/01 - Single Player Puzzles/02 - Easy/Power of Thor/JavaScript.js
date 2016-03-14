var inputs = readline().split(' ');
var LX = parseInt(inputs[0]); // the X position of the light of power
var LY = parseInt(inputs[1]); // the Y position of the light of power
var TX = parseInt(inputs[2]); // Thor's starting X position
var TY = parseInt(inputs[3]); // Thor's starting Y position

for(;;) {
    dY = (TY - LY);
    dX = (TX - LX);
    s = '';

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