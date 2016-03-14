<?php
class Possibility
{
    public $bIsDone = false;
    public $aPossibility = [];

    public function __construct($array)
    {
        $this->aPossibility = $array;
        $this->bIsDone = false;
    }
}

class PossibilityManager
{
    /** @var Possibility[] */
    public $aPossibilities = [];

    public function add(Possibility $oPossibility)
    {
        foreach ($this->aPossibilities as $oStoredPossibility) {
            if ($oStoredPossibility->aPossibility === $oPossibility->aPossibility) {
                return $this;
            }
        }
        $this->aPossibilities[] = $oPossibility;
        return $this;
    }

    public function getNextUndone()
    {
        foreach ($this->aPossibilities as $oPossibility) {
            if ($oPossibility->bIsDone) {
                continue;
            }
            return $oPossibility;
        }
        return null;
    }

    public function getResult()
    {
        $aListPossibilities = [];
        foreach ($this->aPossibilities as $oPossibility) {
            $aListPossibilities[] = implode(' ', $oPossibility->aPossibility);
        }
        sort($aListPossibilities, SORT_STRING);
        return implode(PHP_EOL, $aListPossibilities);
    }
}


$oManager = new PossibilityManager();
fscanf(STDIN, "%d %d", $n, $k);
$oStart = new Possibility(array_fill(0, $n, 1));
$oManager->add($oStart);
while (null !== ($oCurrentPossibility = $oManager->getNextUndone())) {
    $aCurrentPossibility = $oCurrentPossibility->aPossibility;
    if (count($aCurrentPossibility) < 2) {
        $oCurrentPossibility->bIsDone = true;
        continue;
    }
    for ($i=0, $nb=count($aCurrentPossibility)-1; $i<$nb; ++$i) {
        $iSum = array_sum(array_slice($aCurrentPossibility, $i, 2));
        if ($iSum > $k) {
            continue;
        }
        $oNewPossibility = new Possibility(
            array_merge(array_slice($aCurrentPossibility, 0, $i), [$iSum], array_slice($aCurrentPossibility, $i+2))
        );
        $oManager->add($oNewPossibility);
    }
    $oCurrentPossibility->bIsDone = true;
}


echo $oManager->getResult();