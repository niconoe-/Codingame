next_token = string.gmatch(io.read(), "[^%s]+")
LX = tonumber(next_token())
LY = tonumber(next_token())
TX = tonumber(next_token())
TY = tonumber(next_token())

while true do
    dY = TY - LY
    dX = TX - LX
    s = ""

    if dY>0 then
        s = s .. "N"
        TY = TY - 1
    elseif dY<0 then
        s = s .. "S"
        TY = TY + 1
    end

    if dX>0 then
        s = s .. "W"
        TX = TX - 1
    elseif dX<0 then
        s = s .. "E"
        TX = TX + 1
    end
    print(s)
end