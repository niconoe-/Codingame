import System.IO
import Control.Monad
import Data.IORef

main :: IO ()
main = do
    hSetBuffering stdout NoBuffering -- DO NOT REMOVE

    -- Auto-generated code below aims at helping you parse
    -- the standard input according to the problem statement.
    -- ---
    -- Hint: You can use the debug stream to print initialTX and initialTY, if Thor seems not follow your orders.

    input_line <- getLine
    let input = words input_line
    let lx = read (input!!0) :: Int -- the X position of the light of power
    let ly = read (input!!1) :: Int -- the Y position of the light of power
    let tx = read (input!!2) :: Int -- Thor's starting X position
    let ty = read (input!!3) :: Int -- Thor's starting Y position
    loop lx ly tx ty

loop :: Int -> Int -> Int -> Int -> IO ()
loop lx ly tx ty = do
    input_line <- getLine
    let remainingturns = read input_line :: Int -- The remaining amount of turns Thor can move. Do not remove this line.
    let dx = fdx lx tx
    let dy = fdy ly ty
    putStrLn $ sdy dy ++ sdx dx
    loop lx ly (tx + dx) (ty + dy)

fdx lx tx | tx < lx   =  1
          | lx < tx   = -1
          | otherwise =  0

sdx   1  = "E"
sdx (-1) = "W"
sdx   0  = ""

fdy ly ty | ly < ty   = -1
          | ty < ly   =  1
          | otherwise =  0

sdy   1  = "S"
sdy (-1) = "N"
sdy   0  = ""
