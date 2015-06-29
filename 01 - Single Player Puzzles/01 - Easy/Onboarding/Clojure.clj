(ns Player
  (:gen-class))

; Auto-generated code below aims at helping you parse
; the standard input according to the problem statement.

(defn -main [& args]
  (while true
    (let [enemy1 (read) dist1 (read) enemy2 (read) dist2 (read)]
      
      (if (< dist1 dist2) (println enemy1) (println enemy2))
    )
  )
)