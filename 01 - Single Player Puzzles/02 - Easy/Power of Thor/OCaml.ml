let line = input_line stdin in
let lx, ly, tx, ty = Scanf.sscanf line "%d %d %d %d" (fun lx ly tx ty -> (lx, ly, tx, ty)) in

let cx, cy = (ref tx), (ref ty) in

(* game loop *)
while true do
    let remainingturns = int_of_string (input_line stdin) in

    let dx,dy = ref "", ref "" in
    begin
        match !cx with
            |x when x < lx -> (dx := "E"; cx := x+1)
            |x when x > lx -> (dx := "W"; cx := x-1)
            |_             -> ()
    end;
    begin
        match !cy with
            |y when y < ly -> (dy := "S"; cy := y+1)
            |y when y > ly -> (dy := "N"; cy := y-1)
            |_             -> ()
    end;
    print_endline ((!dy)^(!dx));
    ();
done;
