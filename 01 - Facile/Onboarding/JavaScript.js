while (true) {
    var enemy1 = readline();
    var dist1 = parseInt(readline());
    var enemy2 = readline();
    var dist2 = parseInt(readline());
    print((dist1 < dist2) ? enemy1 : enemy2);
}