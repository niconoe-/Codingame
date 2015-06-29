import System.IO
import Control.Monad

main :: IO ()
main = do
    hSetBuffering stdout NoBuffering -- DO NOT REMOVE

    -- CodinGame planet is being attacked by slimy insectoid aliens.
    -- <---
    -- Hint:To protect the planet, you can implement the pseudo-code provided in the statement, below the player.

    loop

loop :: IO ()
loop = do
    input_line <- getLine
    let enemy1 = input_line :: String -- name of enemy 1
    input_line <- getLine
    let dist1 = read input_line :: Int -- distance to enemy 1
    input_line <- getLine
    let enemy2 = input_line :: String -- name of enemy 2
    input_line <- getLine
    let dist2 = read input_line :: Int -- distance to enemy 2

    -- hPutStrLn stderr "Debug messages..."
    if dist1 < dist2 then
        putStrLn enemy1
    else
        putStrLn enemy2

    loop