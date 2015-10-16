while (true) {
    var inputs = readline().split(' ');
    var spaceX = parseInt(inputs[0]);
    var spaceY = parseInt(inputs[1]);

    var iHighest = null;
    var vHighest = 0;

    for (var i = 0; i < 8; i++) {
        var mountainH = parseInt(readline());
        if (vHighest <= mountainH) {
            iHighest = i;
            vHighest = mountainH;
        }
    }

    if (spaceX === iHighest) {
        print("FIRE");
    } else {
        print('HOLD');
    }
}