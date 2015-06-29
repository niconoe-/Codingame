<?php
define('DEBUG', false);
function debug() {
    if (!DEBUG) {
        return;
    }
    foreach (func_get_args() as $sArgDebug) {
        error_log(var_export($sArgDebug, true));
    }
}

class Bataille
{
    public $iNbRound = 0;
    public $aP1Card = array();
    public $aP2Card = array();
    
    public $aP1Stack = array();
    public $aP2Stack = array();
    
    public $bIsPat = false;
    
    public function __construct()
    {
        // the number of cards for player 1
        fscanf(STDIN, "%d", $n);
        $this->aP1Card = [];
        for ($i = 0; $i < $n; ++$i) {
            // the n cards of player 1
            fscanf(STDIN, "%s", $cardp1);
            $this->aP1Card[] = $this->_getValue($cardp1);
        }
        
        // the number of cards for player 2
        fscanf(STDIN, "%d", $m);
        $this->aP2Card = [];
        for ($i = 0; $i < $m; ++$i) {
            // the m cards of player 2
            fscanf(STDIN, "%s", $cardp2);
            $this->aP2Card[] = $this->_getValue($cardp2);
        }
    }
    
    /**
     * Return the value of the card, without taking care about its color
     * @param string $card The card written as value-color (ex: 4H, 8C, AS, ...)
     * @return int The value of the card
     */
    protected function _getValue($card) 
    {
        $value = intval($card);
        if ($value !== 0) {
            return (int)$value;
        }
        $value = substr(trim($card), 0, 1);
        if ($value === 'J') {
            return 11;
        }
        if ($value === 'Q') {
            return 12;
        }
        if ($value === 'K') {
            return 13;
        }
        if ($value === 'A') {
            return 14;
        }
        return (int)$value;
    }
    
    public function getWinner()
    {
        if ($this->bIsPat) {
            return '0'; //Tie
        }
        if (count($this->aP1Card) === 0) {
            return '2'; //Player 2 wins
        }
        if (count($this->aP2Card) === 0) {
            return '1'; //Player 1 wins
        }
        
        return null; //No winners and no tie: the game continue
    }
    
    public function nextRound()
    {
        $this->iNbRound++;
    }
    
    public function play()
    {
        $iValueCardP1 = array_shift($this->aP1Card);
        $iValueCardP2 = array_shift($this->aP2Card);
        
        $this->aP1Stack[] = $iValueCardP1;
        $this->aP2Stack[] = $iValueCardP2;
        
        if ($iValueCardP1 > $iValueCardP2) {
            $this->refreshDeck(1);
        } elseif ($iValueCardP1 < $iValueCardP2) {
            $this->refreshDeck(2);
        } else {
            $this->battle();
        }
    }
    
    public function battle()
    {
        //Battle means slice 3 firsts cards for each player and plays the 4th. 4 cards by players are required
        //debug($this->aP1Card, $this->aP2Card);
        
        //If P1 or P2 has not enought cards to battle, end the game with "PAT"
        if (count($this->aP1Card) < 4 || count($this->aP2Card) < 4) {
            $this->bIsPat = true;
            return;
        }
        
        $this->aP1Stack = array_merge($this->aP1Stack, array_splice($this->aP1Card, 0, 3));
        $this->aP2Stack = array_merge($this->aP2Stack, array_splice($this->aP2Card, 0, 3));
        
        //Then, play as usual
        $this->play();
    }
    
    public function refreshDeck($playerWinner)
    {
        if ($playerWinner === 1) {
            $this->aP1Card = array_merge($this->aP1Card, array_merge($this->aP1Stack, $this->aP2Stack));
        } elseif ($playerWinner === 2) {
            $this->aP2Card = array_merge($this->aP2Card, array_merge($this->aP1Stack, $this->aP2Stack));
        }
    }
    
    public function cleanStack()
    {
        $this->aP1Stack = [];
        $this->aP2Stack = [];
    }
}

//----------------------------------------------------------------------------------------------------------------------------------

$oBataille = new Bataille();
while (true) {
    if (null !== ($sWinner = $oBataille->getWinner())) {
        break;
    }
    $oBataille->play();
    $oBataille->cleanStack();
    $oBataille->nextRound();
}

echo ($sWinner === '0') ? "PAT\n" : ($sWinner . ' ' . $oBataille->iNbRound . "\n");
?>