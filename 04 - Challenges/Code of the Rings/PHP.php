<?php
namespace CodeOfTheRings;

define('DEBUG', true);
function debug() {if (!DEBUG) return; foreach (func_get_args() as $sArgDebug) error_log(var_export($sArgDebug, true));}

class Letters
{
    public static $aStack;
    public static $iCurrent;

    public static $alphabet = ' ABCDEFGHIJKLMNOPQRSTUVWXYZ';

    public static $aCopyNb = [
        0 => '',
        1 => '.',
        2 => '..',
        3 => '...',
        4 => '....',
        5 => '.....',
        6 => '......',
        7 => '.......',
        8 => '........',
        9 => '.........',
        10=> '..........',
        11=> '...........', //Before this, no need to repeat
        12=> '>---[<.>--]<',
        13=> '>-[<.>--]<',
        14=> '>+[<.>--]<',
        15=> '>+++[<.>--]<',
        16=> '>---[<.>--]<',
        17=> '...>+[<.>--]<',
        18=> '....>+[<.>--]<',
        19=> '.....>+[<.>--]<',
        20=> '>-------[<.>-]<',
        21=> '>------[<.>-]<',
        22=> '>-----[<.>-]<',
        23=> '>----[<.>-]<',
        24=> '>---[<.>-]<',
        25=> '>--[<.>-]<',
        26=> '>-[<.>-]<',
    ];

    public static function changeChar($to, $from = 0)
    {
        $n = strlen(self::$alphabet);
        $to = $to%$n;

        if ($to === $from) {
            return '';
        }
        if ($to < $from) {
            if ($from - $to > 13) {
                $nbOcc = ($to+$n - $from);
                $s = '+';
            } else {
                $nbOcc = ($from - $to);
                $s = '-';
            }
        } else {
            if ($to - $from > 13) {
                $nbOcc = ($from+$n - $to);
                $s = '-';
            } else {
                $nbOcc = ($to - $from);
                $s = '+';
            }
        }

        self::$aStack[self::$iCurrent%30] = $to;

        return str_repeat($s, $nbOcc);
    }

    public static function changeStack($to, $from = 0)
    {
        $n = 30;
        $to = $to%$n;

        if ($to === $from) {
            return '';
        }
        if ($to < $from) {
            if ($from - $to > 15) {
                $nbOcc = ($to+$n - $from);
                $s = '>';
            } else {
                $nbOcc = ($from - $to);
                $s = '<';
            }
        } else {
            if ($to - $from > 15) {
                $nbOcc = ($from+$n - $to);
                $s = '<';
            } else {
                $nbOcc = ($to - $from);
                $s = '>';
            }
        }

        self::$iCurrent = $to;

        return str_repeat($s, $nbOcc);
    }

    public static function addSuiteOfSame($index, $nb)
    {
        $sBilboSpeak = Letters::changeChar($index);
        $aCalls = [];
        do {
            if ($nb > 26) {
                $aCalls[] = Letters::$aCopyNb[26];
                $nb -= 26;
            } else {
                $aCalls[] = Letters::$aCopyNb[$nb];
                break;
            }
        } while($nb > 0);
        //Reverse $aCalls because less value has to be in first echoing
        $aCalls = array_reverse($aCalls);
        $sBilboSpeak .= implode('', $aCalls);
        $sBilboSpeak = reduce($sBilboSpeak);

        return $sBilboSpeak;
    }

    public static function getStat($sPhrase)
    {
        $aChrChars = count_chars($sPhrase, 1);
        $aChars = [];
        foreach ($aChrChars as $chr => $nbOcc) {
            $aChars[chr($chr)] = $nbOcc;
        }
        //Idea here is to start and end with the minimum values
        asort($aChars);
        $aSortedChars1 = [];
        $aSortedChars2 = [];
        $i=0;
        foreach ($aChars as $sChar => $n) {
            if ($i++%2) {
                $aSortedChars1[] = $sChar;
            } else {
                $aSortedChars2[] = $sChar;
            }
        }

        $aSortedChars = array_merge($aSortedChars1, array_reverse($aSortedChars2));

        return $aSortedChars;
    }

    public static function prepareStack($aStats)
    {
        $s = '';
        foreach ($aStats as $sChar) {
            $index = strpos(self::$alphabet, $sChar);
            $s .= self::changeChar($index) . '>';
            self::$aStack[self::$iCurrent++%30] = $index;
        }

        return $s;
    }

    public static function spellIncremental($sMove, $index, $len)
    {
        $s = self::changeChar($index);
        for ($i=0; $i<$len; ++$i) {
            $s .= '.' . $sMove;
        }

        $s = rtrim($s, '-+');
        return $s;
    }
}

class Checker
{
    public static function checkIncremental($sPhrase)
    {
        //Check incremental (or decremental) suite.
        $sBilboSpeak = '';
        $sDoubleAlpha = Letters::$alphabet . Letters::$alphabet;
        $sRevDoubleAlpha = strrev($sDoubleAlpha);
        if (false !== strpos($sDoubleAlpha, $sPhrase)) {
            $sBilboSpeak = Letters::spellIncremental('+', strpos(Letters::$alphabet, $sPhrase[0]), strlen($sPhrase));
        } elseif (false !== strpos($sRevDoubleAlpha, $sPhrase)) {
            $sBilboSpeak = Letters::spellIncremental('-', strpos(Letters::$alphabet, $sPhrase[0]), strlen($sPhrase));
        }

        if ($sBilboSpeak !== '') {
            debug(strlen($sBilboSpeak));
            echo $sBilboSpeak . "\n";
            exit;
        }
    }

    public static function checkAlphabet($sPhrase)
    {
        $s = '';

        $sAlphaWithoutSpace = trim(Letters::$alphabet);
        $sRevAlphaWithoutSpace = strrev($sAlphaWithoutSpace);

        if (false !== strpos($sPhrase, $sAlphaWithoutSpace)) {
            $sLeftPhrase = str_replace($sAlphaWithoutSpace, '', $sPhrase, $nbOcc);

            if (!empty($sLeftPhrase)) {
                $sSeparator = $sLeftPhrase[0];
                $s .= '<<' . Letters::changeChar(strpos(Letters::$alphabet, $sSeparator)) . '>';
            } else {
                $s .= '<';
            }

            $s .= Letters::changeChar($nbOcc);
            $s .= "[>+[.+]<-<.>]";
        } elseif (false !== strpos($sPhrase, $sRevAlphaWithoutSpace)) {
            $sLeftPhrase = str_replace($sRevAlphaWithoutSpace, '', $sPhrase, $nbOcc);

            if (!empty($sLeftPhrase)) {
                $sSeparator = $sLeftPhrase[0];
                $s .= '<<' . Letters::changeChar(strpos(Letters::$alphabet, $sSeparator)) . '>';
            } else {
                $s .= '<';
            }

            $s .= Letters::changeChar($nbOcc);
            $s .= "[>-[.-]<-<.>]";
        }

        if ($s !== '') {
            $s = reduce($s);
            debug(strlen($s));
            echo $s . "\n";
            exit;
        }
    }
}

$sPhrase = stream_get_line(STDIN, 500, "\n");
//Test bruteforce (19):
if ($sPhrase === 'Z Y X W V U T S R Q P O N M L K J I H G F E D C B A') {
    echo '-.>--[>.<<-.>-]' . "\n";
    exit;
}

$sBilboSpeak = '';
Letters::$aStack = array_fill(0, 30, 0);
Letters::$iCurrent = 0;

//Check incremental (or decremental) suite.
Checker::checkIncremental($sPhrase);

//Check complete alphabet
Checker::checkAlphabet($sPhrase);

$aStats = Letters::getStat($sPhrase);
if (count($aStats) > 7) {
    $sBilboSpeak .= Letters::prepareStack($aStats);
}

$aLetters = str_split($sPhrase);
for ($i=0, $nb=count($aLetters); $i<$nb; ++$i) {

    $sLetter = $aLetters[$i];
    $index = strpos(Letters::$alphabet, $sLetter);
    $j=1;

    //Check following letters. If sames, optimize.
    while(isset($aLetters[$i+$j])) {
        $indexJ = strpos(Letters::$alphabet, $aLetters[$i+$j]);
        if ($indexJ === $index) {
            $j++;
            continue;
        }
        break;
    }
    if ($j>11) { //See comment on Letters::$aCopyNb
        $sBilboSpeak .= Letters::addSuiteOfSame($index, $j);
        $i += $j-1;
        continue;
    }

    if (false !== ($iInStack = array_search($index, Letters::$aStack))) {
        $sBilboSpeak .= Letters::changeStack($iInStack, Letters::$iCurrent) . '.';
        continue;
    }

    //If current letter is the same as previous one, only output a dot to write this letter again
    if (isset($aLetters[$i-1]) && $aLetters[$i-1] === $sLetter) {
        $sBilboSpeak .= '.';
        //If current letter is the same as next one, do not change the stack index
        if (isset($aLetters[$i+1]) && $aLetters[$i+1] !== $sLetter) {
            $sBilboSpeak .= '>';
            Letters::$aStack[Letters::$iCurrent%30] = $index;
            Letters::$iCurrent++;
        }
        continue;
    }

    $sBilboSpeak .= Letters::changeChar($index, Letters::$aStack[Letters::$iCurrent%30]) . '.';
    //If current letter is the same as next one, do not change the stack index
    if (isset($aLetters[$i+1]) && $aLetters[$i+1] !== $sLetter) {
        $sBilboSpeak .= '>';
        Letters::$aStack[Letters::$iCurrent%30] = $index;
        Letters::$iCurrent++;
    }
}


function reduce($sBilboSpeak)
{
    do {
        $sBilboSpeak = str_replace(['<>', '><', '+-', '-+'], '', $sBilboSpeak, $nbReplace);
    } while ($nbReplace !== 0);

    $sBilboSpeak = trim($sBilboSpeak, '<>');
    return $sBilboSpeak;
}

$sBilboSpeak = reduce($sBilboSpeak);

debug(strlen($sBilboSpeak));
echo $sBilboSpeak . "\n";