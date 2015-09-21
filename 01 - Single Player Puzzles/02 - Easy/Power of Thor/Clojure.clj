(ns Player
  (:gen-class))

(defn -main [& args]
  (let [LX (read) LY (read) TX (atom (read)) TY (atom (read))]
    (while true
      (def dY (- @TY LY))
      (def dX (- @TX LX))
      (def s "")

      ;(binding [*out* *err*] (println "dY: " dY " - TY: " @TY))
      ;(binding [*out* *err*] (println "dX: " dX " - TX: " @TX))

      (when (> dY 0)
        (def s (clojure.string/join [s "N"]))
        (swap! TY #(- %1 1))
      )
      (when (< dY 0)
        (def s (clojure.string/join [s "S"]))
        (swap! TY #(+ %1 1))
      )

      (when (> dX 0)
        (def s (clojure.string/join [s "W"]))
        (swap! TX #(- %1 1))
      )
      (when (< dX 0)
        (def s (clojure.string/join [s "E"]))
        (swap! TX #(+ %1 1))
      )

      (println s)
    )
  )
)