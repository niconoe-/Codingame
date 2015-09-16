STDOUT.sync = true # DO NOT REMOVE
LX, LY, TX, TY = gets.split(" ").collect {|x| x.to_i}

# game loop
loop do
    dY = TY - LY
    dX = TX - LX
    s = ""

    if dY > 0 then
        s += "N"
        TY -= 1
    elsif  dY < 0 then
        s += "S"
        TY += 1
    end

    if dX > 0 then
        s += "W"
        TX -= 1
    elsif dX < 0 then
        s += "E"
        TX += 1
    end

    puts s
end