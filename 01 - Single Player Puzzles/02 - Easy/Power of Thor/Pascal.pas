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
    LX : Int32; // the X position of the light of power
    LY : Int32; // the Y position of the light of power
    TX : Int32; // Thor's starting X position
    TY : Int32; // Thor's starting Y position
    remainingTurns : Int32;
    Inputs: TStringList;
    dX : Int32;
    dY : Int32;
    s : string;
begin
    Inputs := TStringList.Create;
    ParseIn(Inputs);
    LX := StrToInt(Inputs[0]);
    LY := StrToInt(Inputs[1]);
    TX := StrToInt(Inputs[2]);
    TY := StrToInt(Inputs[3]);

    // game loop
    while true do
    begin
        dY := TY - LY;
        dX := TX - LX;
        s := '';

        if (dY > 0) then
        begin
            s := s + 'N';
            TY := TY-1;
        end
        else if dY < 0 then
        begin
            s := s + 'S';
            TY := TY+1;
        end;

        if dX > 0 then
        begin
            s := s + 'W';
            TX := TX-1;
        end
        else if dX < 0 then
        begin
            s := s + 'E';
            TX := TX+1;
        end;

        writeln(s);
        flush(StdErr); flush(output); // DO NOT REMOVE
    end;
end.