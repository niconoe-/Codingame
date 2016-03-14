open System

let token = (Console.In.ReadLine()).Split [|' '|]
let LX = int(token.[0])
let LY = int(token.[1])
let mutable TX = int(token.[2])
let mutable TY = int(token.[3])

(* game loop *)
while true do
    let remainingTurns = int(Console.In.ReadLine()) (* The remaining amount of turns Thor can move. Do not remove this line. *)

    let dY = int(TY - LY)
    let dX = int(TX - LX)
    let mutable s = ""

    if (dY > 0) then
        s <- s + "N"
        TY <- TY - 1
    elif (dY < 0) then
        s <- s + "S"
        TY <- TY + 1

    if (dX > 0) then
        s <- s + "W"
        TX <- TX - 1
    elif (dX < 0) then
        s <- s + "E"
        TX <- TX + 1

    printfn "%s" s
    ()
