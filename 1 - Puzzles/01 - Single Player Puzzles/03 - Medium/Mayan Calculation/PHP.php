<?php
define('DEBUG', true);
function debug() {if (!DEBUG) return; foreach (func_get_args() as $sArgDebug) error_log(var_export($sArgDebug, true));}

function getNumerical($aNumbers, $aLines)
{
    $iValueReturn = 0;
    foreach ($aLines as $pow => $sLineValue) {
        $iDigit = array_search($sLineValue, $aNumbers);
        $iValueReturn += ($iDigit * pow(20, $pow));
    }
    
    return $iValueReturn;
}

function getMaya($aNumbers, $iValue)
{
    if ($iValue < 20) {
        return array($aNumbers[$iValue]);
    }
    $iRemainder = $iValue % 20;
    $iQuotient = ($iValue - $iRemainder) / 20;
    
    return array_merge(getMaya($aNumbers, $iQuotient), array($aNumbers[$iRemainder]));
}


//Largeur et Hauteur d'un chiffre Maya.
fscanf(STDIN, "%d %d", $L, $H);

$aNumbers = array_fill(0, 20, '');
for ($i = 0; $i < $H; ++$i) {
    fscanf(STDIN, "%s", $numeral);
    foreach (str_split($numeral, $L) as $number => $lineOfNumber) {
        $aNumbers[$number] .= $lineOfNumber;
    }
}
debug($aNumbers);

//Nombre de lignes du premier terme de l'opération
fscanf(STDIN, "%d", $S1);
$aS1Lines = array('');
for ($i = 0; $i < $S1; ++$i) {
    fscanf(STDIN, "%s", $num1Line);
    if ($i !== 0 && $i%$L === 0) {
        array_unshift($aS1Lines, '');
    }
    $aS1Lines[0] .= $num1Line;
}
debug($aS1Lines);

//Nombre de lignes du deuxieme terme de l'opération
fscanf(STDIN, "%d", $S2);
$aS2Lines = array('');
for ($i = 0; $i < $S2; ++$i) {
    fscanf(STDIN, "%s", $num2Line);
    if ($i !== 0 && $i%$L === 0) {
        array_unshift($aS2Lines, '');
    }
    $aS2Lines[0] .= $num2Line;
}
debug($aS2Lines);

$iValue1 = getNumerical($aNumbers, $aS1Lines);
$iValue2 = getNumerical($aNumbers, $aS2Lines);

//L'opération (*, /, + ou -)
fscanf(STDIN, "%s", $operation);
if ($operation === '+') {
    $iValueResult = $iValue1 + $iValue2;
} elseif ($operation === '-') {
    $iValueResult = $iValue1 - $iValue2;
} elseif ($operation === '*') {
    $iValueResult = $iValue1 * $iValue2;
} elseif ($operation === '/') {
    if ($iValue2 === 0) {
        throw new InvalidArgumentException('Try to run a division by zero.');
    }
    $iValueResult = $iValue1 / $iValue2;
} else {
    throw new InvalidArgumentException($operation . ' is not a good operator.');
}

$aLinesResults = getMaya($aNumbers, $iValueResult);
$sFullResult = implode('', $aLinesResults);
echo wordwrap($sFullResult, $L, "\n", true) . "\n";

?>