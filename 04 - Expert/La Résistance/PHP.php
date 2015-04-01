<?php
namespace La_Resistance;

define('DEBUG', true);
function debug() {if (!DEBUG) return; foreach (func_get_args() as $sArgDebug) error_log(var_export($sArgDebug, true));}

/**
 * Class Morse
 *
 * @package La_Resistance
 * @author Nicolas (niconoe) Giraud <nicolas.giraud.dev@gmail.com>
 * @copyright Copyright © 2015, Nicolas Giraud
 */
class Morse
{

    /**
     * @var array[] Contains, for each position in the sequence, the list of words that fits, and the number of
     * occurrence
     */
    public $aWordsPosition = [];

    /**
     * @var string[] The list of Morse code
     */
    public $aMorse = [
        'A' => '.-',   'B' => '-...', 'C' => '-.-.', 'D' => '-..',  'E' => '.',    'F' => '..-.', 'G' => '--.',
        'H' => '....', 'I' => '..',   'J' => '.---', 'K' => '-.-',  'L' => '.-..', 'M' => '--',   'N' => '-.',
        'O' => '---',  'P' => '.--.', 'Q' => '--.-', 'R' => '.-.',  'S' => '...',  'T' => '-',    'U' => '..-',
        'V' => '...-', 'W' => '.--',  'X' => '-..-', 'Y' => '-.--', 'Z' => '--..',
    ];

    /**
     * @var string The Morse sequence to analyze
     */
    public $sequence;

    /**
     * @var int The length of the sequence. Used to avoid counting it for each analyze.
     */
    public $seqLength;

    /**
     * Create the Morse instance that contains the morse string to analyse and the dictionary
     */
    public function __construct()
    {
        fscanf(STDIN, "%s", $this->sequence);
        $this->seqLength = strlen($this->sequence);
        fscanf(STDIN, "%d", $nbWords);
        for ($i = 0; $i < $nbWords; ++$i) {
            fscanf(STDIN, "%s", $sWord);
            $this->addWord($this->convertWord($sWord));
        }
    }

    /**
     * Convert word into Morse expression
     * @param string $W
     * @return string
     */
    public function convertWord($W) {
        $morse = '';
        for ($i=0, $len=strlen($W); $i<$len; ++$i) {
            $morse .= $this->aMorse[$W[$i]];
        }
        return $morse;
    }

    /**
     * Add the word in the positions array only if the word is present in the sequence.
     * For each positions, each word the number of occurrences.
     * @param string $sWord The word in morse transcription.
     */
    public function addWord($sWord)
    {
        $iLastPos = 0;
        while (false !== ($iLastPos = strpos($this->sequence, $sWord, $iLastPos))) {
            isset($this->aWordsPosition[$iLastPos]) ?: $this->aWordsPosition[$iLastPos] = [];
            //Store them is separated array because words can be similar in morse and they have to be multiplied.
            $this->aWordsPosition[$iLastPos][] = ['word' => $sWord];
            $iLastPos = $iLastPos + 1;
        }
    }

    /**
     * Count the occurrences of all words for all positions that fit perfectly the sequence.
     * @param int $iStartPos
     * @return int|null
     */
    public function countOccurrences($iStartPos = 0)
    {
        //If the position we're analyzing is end of sequence, all words fit for this analyze so return 1 occurrence.
        if ($iStartPos === $this->seqLength) {
            return 1;
        }

        //Else if we're "really" out of the sequence (i.e. it miss some chars to fit) it fails so return 0 occurrence.
        if (!isset($this->aWordsPosition[$iStartPos])) {
            return 0;
        }

        //Here, we're in the sequence. let's analyze the occurrences of the word in the sequence
        $iOccurrences = 0;
        foreach ($this->aWordsPosition[$iStartPos] as $i => &$aWordInfo) {
            $sWord = $aWordInfo['word'];
            $iWordOccurrences = isset($aWordInfo['occurrences']) ? $aWordInfo['occurrences'] : null;
            if (null !== $iWordOccurrences) {
                $iOccurrences += $iWordOccurrences;
                continue;
            }

            //If we don't know the number of occurrences that may fit with the whole sequence depending on the other
            //words, let's find this value by analyzing the rest of the sequence
            $iWordOccurrences = $this->countOccurrences($iStartPos + strlen($sWord));
            $iOccurrences += $iWordOccurrences;
            //Replace the new occurrences for the given word.
            $aWordInfo['occurrences'] = $iWordOccurrences;
        } unset($aWordInfo);

        return $iOccurrences;
    }
}

//Create the Morse instance that contains the morse string to analyse and the dictionary
$oMorse = new Morse();
//Count the words we can make and display the result as the answer.
echo $oMorse->countOccurrences() . "\n";

