// Auto-generated code below aims at helping you parse
// the standard input according to the problem statement.
program Answer;
{$H+}
uses sysutils, classes, math;

// Helper to read a line and split tokens
procedure ParseIn(Inputs: TStrings) ;
var Line : string;
begin
    readln(Line);
    Inputs.Clear;
    Inputs.Delimiter := ' ';
    Inputs.DelimitedText := Line;
end;

var
    enemy1 : String;
    dist1 : Int32;
    enemy2 : String;
    dist2 : Int32;
    Inputs: TStringList;
begin
    Inputs := TStringList.Create;

    // game loop
    while true do
    begin
        ParseIn(Inputs);
        enemy1 := Inputs[0];
        ParseIn(Inputs);
        dist1 := StrToInt(Inputs[0]);
        ParseIn(Inputs);
        enemy2 := Inputs[0];
        ParseIn(Inputs);
        dist2 := StrToInt(Inputs[0]);

        if dist1 < dist2 then writeln(enemy1) else writeln(enemy2) ;
        flush(StdErr); flush(output); // DO NOT REMOVE
    end;
end.