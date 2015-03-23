(ns Solution
  (:gen-class))

; Auto-generated code below aims at helping you parse
; the standard input according to the problem statement.

(defmacro for-loop [[sym init check change :as params] & steps]
 `(loop [~sym ~init value# nil]
    (if ~check
      (let [new-value# (do ~@steps)]
        (recur ~change new-value#))
      value#)))

(defn -main [& args]
    (defn ^Integer sum [aGroups] (reduce + aGroups))
    (def aGroups [])
  (let [L (read) C (read) N (read)]
    (loop [i N]
      (when (> i 0)
        (let [Pi (read)]    
        (def aGroups (conj aGroups Pi))
        (recur (dec i)))
        
    ))
    
    ;Optimisation: if all people of all groups can go in one time, 
    ;result is nbPeople * nb run in a day
    (when (> L (sum aGroups)) 
        (println (* C (sum aGroups))) 
        (System/exit 0)
    )
    
    (def bFoundRepetition false)
    (def nbDirham [0])
    (def aHistoGroups [aGroups])
    (def i (atom 1))
    
      (while (<= @i C)
        (def currentL 0)
        (def bReachMax false)
        (def aGroupsInTrain [])
        (while (= true (not bReachMax) (not (empty? aGroups))) (do
            (def nbPeople (first aGroups))
            (def aGroups (rest aGroups))
            (def aGroups (into [] aGroups))
            (when (< L (+ currentL nbPeople))
              (def aGroups (into [nbPeople] aGroups))
              (def bReachMax true)
            )
            (when (>= L (+ currentL nbPeople))
              (def currentL (+ currentL nbPeople))
              (def aGroupsInTrain (conj aGroupsInTrain nbPeople))
            )
        ))
    
        (def nbDirham (conj nbDirham (sum aGroupsInTrain)))
        (def aGroups (into aGroups aGroupsInTrain))
        
        (when (= bFoundRepetition false)
            (def iFound (+ 1 (.indexOf aHistoGroups aGroups)))
            (when (< 0 iFound)
                (def iSameNbDirham (+ 1 (- @i iFound)))
                (def aSameNbDirham (subvec nbDirham iFound (+ iFound iSameNbDirham)))
                (def nbRoundsLeft (- C @i))
                (def nbSequences (quot nbRoundsLeft iSameNbDirham))
                
                (when (> nbSequences 0)
                    (swap! i #(+ %1 (* iSameNbDirham nbSequences)))
                    (def addDirham (* (sum aSameNbDirham) nbSequences))
                    (def nbDirham (conj nbDirham addDirham))
                    (def bFoundRepetition true)
                )
            )
            (when (= 0 iFound)
                (def aHistoGroups (conj aHistoGroups aGroups))
            )
        )
        (swap! i inc)
      )

    (def totalWin (sum nbDirham))
    (println totalWin)
    ;(binding [*out* *err*] (println ...))
  )
)