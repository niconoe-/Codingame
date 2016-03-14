STDOUT.sync = true # DO NOT REMOVE
# Auto-generated code below aims at helping you parse
# the standard input according to the problem statement.


# game loop
loop do
    space_x, space_y = gets.split(" ").collect {|x| x.to_i}
    i_h = -1
    v_h = 0
    8.times do |i|
        mountain_h = gets.to_i # represents the height of one mountain, from 9 to 0. Mountain heights are provided from left to right.
        if v_h <= mountain_h then
            i_h = i
            v_h = mountain_h
        end
    end

    if space_x == i_h then
        puts "FIRE"
    else
        puts "HOLD"
    end
end