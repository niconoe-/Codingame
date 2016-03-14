Module Player
    Sub Main ()

        Dim inputs as String()
        Dim LX as Integer ' the X position of the light of power
        Dim LY as Integer ' the Y position of the light of power
        Dim TX as Integer ' Thor's starting X position
        Dim TY as Integer ' Thor's starting Y position
        inputs = Console.ReadLine().Split(" ")
        LX = inputs(0)
        LY = inputs(1)
        TX = inputs(2)
        TY = inputs(3)

        ' game loop
        While True
            Dim dY as Integer
            Dim dX as Integer
            Dim s as String

            dY = TY-LY
            dX = TX-LX
            s = ""

            If dY>0
                s += "N"
                TY -= 1
            ElseIf dY<0
                s += "S"
                TY += 1
            End If

            If dX>0
                s += "W"
                TX -= 1
            ElseIf dX<0
                s += "E"
                TX += 1
            End If

            Console.WriteLine(s)
        End While
    End Sub
End Module