(* CodinGame planet is being attacked by slimy insectoid aliens. *)
(* <--- *)
(* Hint:To protect the planet, you can implement the pseudo-code provided in the statement, below the player. *)


(* game loop *)
while true do
    let enemy1 = input_line stdin in (* name of enemy 1 *)
    let dist1 = int_of_string (input_line stdin) in (* distance to enemy 1 *)
    let enemy2 = input_line stdin in (* name of enemy 2 *)
    let dist2 = int_of_string (input_line stdin) in (* distance to enemy 2 *)

    (* Write an action using print_endline *)
    (* To debug: prerr_endline "Debug message"; *)

    if dist1 < dist2 then print_endline enemy1
    else print_endline enemy2;
    ();
done;