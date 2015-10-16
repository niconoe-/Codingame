(ns Player
  (:gen-class))

(defn -main [& args]
  (while true
    (let [spaceX (read) spaceY (read)]
      (def iHighest -1)
      (def vHighest 0)
      (loop [i 8]
        (when (> i 0)
          (let [mountainH (read)]

            (when (<= vHighest mountainH)
              (def iHighest (- 8 i))
              (def vHighest mountainH)
            )

            (recur (dec i))
          )
        )
      )

      (println (if (= spaceX iHighest) "FIRE" "HOLD"))
    )
  )
)