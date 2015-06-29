<?php
define('DEBUG', true);
function debug() {if (!DEBUG) return; foreach (func_get_args() as $sArgDebug) error_log(var_export($sArgDebug, true));}

class Gift
{
    public $aBuds = array();
    public $aSortedBuds = array();
    public $iTotalMoney = 0;
    
    public $iGiftValue = null;
    public $iNbOods = null;
    
    public $aGives = array();
    
    public function __construct($n, $c)
    {
        $this->iNbOods = $n;
        $this->iGiftValue = $c;
    }
    
    public function addBud($b, $i)
    {
        $this->aBuds[$i] = $b;
        $this->iTotalMoney += $b;
    }
    
    public function isImpossible()
    {
        return ($this->iTotalMoney < $this->iGiftValue);
    }
    
    public function process()
    {
        $reste = $this->iGiftValue % $this->iNbOods;
        $quotient = ($this->iGiftValue - $reste) / $this->iNbOods;
        
        $bCanEveryonePay = true;
        foreach ($this->aBuds as $indexOod => $iBud) {
            if ($iBud <= $quotient) {
                $this->aGives[$indexOod] = $iBud;
                unset($this->aBuds[$indexOod]);
                $this->iNbOods--;
                $this->iGiftValue -= $iBud;
                $bCanEveryonePay = false;
            }
        }
        
        //If everyone can pay (means every oods own more than the average)
        if ($bCanEveryonePay) {
            //Everyone give the quotient (the average)
            foreach ($this->aBuds as $indexOod => $iBud) {
                $this->aGives[$indexOod] = $quotient;
            }
            //Puis le reste est équitablement réparti
            reset($this->aBuds);
            for ($i=0; $i<$reste; ++$i) {
                $key = key($this->aBuds);
                $this->aGives[$key]++;
                next($this->aBuds);
            }
            
            debug('Everyone can pay', $this->aGives, $this->aBuds);
            return true;
        } else {
            return $this->process();
        }
    }
    
    public function getSortedGives()
    {
        sort($this->aGives);
        return implode("\n", $this->aGives);
    }
}


fscanf(STDIN, "%d", $N); //Nb Oods 0 < N <= 2 000
fscanf(STDIN, "%d", $C); //Gift price 0 < C <= 1 000 000 000
$oGift = new Gift($N, $C);

for ($i = 0; $i < $N; ++$i) {
    fscanf(STDIN, "%d", $B); //Budget for each Oods 0 < B <= 1 000 000 000
    $oGift->addBud($B, $i);
}

//Test Impossible case: not enough money to pay the gift
if ($oGift->isImpossible()) {
    echo "IMPOSSIBLE\n";
    return;
}


if ($oGift->process()) {
    echo $oGift->getSortedGives() . "\n";
    return;
}


?>