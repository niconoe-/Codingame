while true do

    let line = input_line stdin in
    let spacex, spacey = Scanf.sscanf line "%d %d" (fun spacex spacey -> (spacex, spacey)) in
    let ihigh,vhigh = ref (-1), ref 0 in

    for i = 0 to 7 do
        let mountainh = int_of_string (input_line stdin) in
        begin
            match !vhigh with
                |v when v <= mountainh -> (ihigh := i; vhigh := mountainh)
                |_                     -> ()
        end;

        ();
    done;

    if spacex == !ihigh then
        print_endline "FIRE"
    else
        print_endline "HOLD";
    ();
done;