Module Player
    Sub Main ()
        While True
            Dim enemy1 as String
            enemy1 = Console.ReadLine()

            Dim dist1 as Integer
            dist1 = Console.ReadLine()

            Dim enemy2 as String
            enemy2 = Console.ReadLine()

            Dim dist2 as Integer
            dist2 = Console.ReadLine()

            If dist1 < dist2 Then
                Console.WriteLine(enemy1)
            Else
                Console.WriteLine(enemy2)
            End If
        End While
    End Sub
End Module