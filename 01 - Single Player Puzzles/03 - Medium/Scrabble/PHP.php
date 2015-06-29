<?php
define('DEBUG', true);
function debug() {if (!DEBUG) return; foreach (func_get_args() as $sArgDebug) error_log(var_export($sArgDebug, true));}

class Alphabet
{
    public static $aValues = array(
        'a' => 1, 'b' => 3, 'c' => 3, 'd' => 2, 'e' => 1, 'f' => 4, 'g' => 2,  'h' => 4, 'i' => 1, 'j' => 8,
        'k' => 5, 'l' => 1, 'm' => 3, 'n' => 1, 'o' => 1, 'p' => 3, 'q' => 10, 'r' => 1, 's' => 1, 't' => 1,
        'u' => 1, 'v' => 4, 'w' => 4, 'x' => 8, 'y' => 4, 'z' => 10,
    );

    public static function getWordValue($word)
    {
        $aWordLetters = str_split(strtolower($word));
        $value = 0;
        foreach ($aWordLetters as $sLetter) {
            $value += self::$aValues[$sLetter];
        }
        return $value;
    }
}

class Dictionnary
{

    public $aDico = array();

    public function addWord($word)
    {
        $value = Alphabet::getWordValue($word);

        //Only the first word has to be returned so if another word has the same value, don't add it.
        if (!isset($this->aDico[$word])) {
            $this->aDico[$word] = array('value' => $value, 'pos' => count($this->aDico));
        }
        return $this;
    }

    public function sortDictionnary()
    {
        uasort($this->aDico, array($this, '_sort'));
        return $this;
    }

    protected function _sort($a, $b)
    {
        if ($a['value'] === $b['value']) {
    		if ($a['pos'] === $b['pos']) {
    			return 0;
    		}
    		return ($a['pos'] < $b['pos'] ? -1 : 1);
    	}
    	return ($a['value'] < $b['value'] ? 1 : -1);
    }

    public function findMostValuable($letters)
    {
        //Sort words from the dictionnary by the most valuable first
        $this->sortDictionnary();

        //For each word, try to know if we can write it depending on the letters the player owns.
        foreach (array_keys($this->aDico) as $sWord) {
            $aAvailableLetters = str_split($letters); //reset the available letter for each word to test.
            $aWordLetters = str_split($sWord);
            $bNotThisWord = false;
            foreach ($aWordLetters as $sWordLetter) {
                $iLetterKey = array_search($sWordLetter, $aAvailableLetters);
                //If the word contains a letter the player doesn't own, impossible to get the word.
                if (false === $iLetterKey) {
                    $bNotThisWord = true;
                    break; //Let's try another word.
                }
                //If the word contains a letter the player own, the player remove this letter from his play-tab.
                unset($aAvailableLetters[$iLetterKey]);
            }
            //After trying to fit the player's letters to a word, if the word is playable, this is the answer
            if (!$bNotThisWord) {
                return $sWord;
            }
        }

        return '';
    }

}

//Start programm

//Create a dictionnary
$oDico = new Dictionnary();

//For each word, try to add it in the dictionnary.
fscanf(STDIN, "%d", $N);
for ($i = 0; $i < $N; ++$i) {
    $W = stream_get_line(STDIN, 31, "\n");
    //Impossible to play a word with more than 7 letters, so don't store it.
    if (strlen($W) <= 7) {
        $oDico->addWord($W);
    }
}

//$oDico->sortDictionnary();

//Store the letters the players own.
$LETTERS = stream_get_line(STDIN, 7, "\n");

$sMostValuableWord = $oDico->findMostValuable($LETTERS);
echo $sMostValuableWord . "\n";

?>