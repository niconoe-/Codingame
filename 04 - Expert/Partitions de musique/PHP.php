<?php
define('DEBUG', true);
function debug() {if (!DEBUG) return; foreach (func_get_args() as $sArgDebug) error_log(var_export($sArgDebug, true));}
function e($m){echo "$m\n";}

class Partition
{
    /**
     * @var int $w. Width of the picture. 100 < $w < 5000
     */
    public $w;
    
    /**
     * @var int $h. Height of the picture. 70 < $h < 300
     */
    public $h;
    
    /**
     * @var array[] $aPic. Array of arrays that worth 0 for a white pixel and 1 for a black pixel. 
     * 1st dimension is for columns and 2nd dimension is for rows.
     */
    public $aPic = [];
    
    public $aColRef = [];
    public $aColRefC = [];
    
    public $aRefLines = [];
    public $iThinLine = 0;
    public $iGap = 0;
    
    public $aNotes = [];
    
    public function __construct()
    {
        fscanf(STDIN, "%d %d", $this->w, $this->h);
        
        //Picture is composed with a maximum of 5.000*300 = 1.500.000 pixels.
        //In the worst scenario, each pixel is switching from Black to White, resulting of a DWE of 750.000 "W 1 B 1 " (string(8))
        //So, the longest DWE encoding is 750.000*8 = 6.000.000 characters.
        $sPic = stream_get_line(STDIN, 6000000, "\n");
        $this->aPic = $this->_transpose($sPic);
    }
    
    private function _transpose($sPic) {
        $aColorsNb = preg_split('#([^ ]* [^ ]*) #', $sPic, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
        $sBinaryPic = '';
        foreach ($aColorsNb as $sColorNb) {
            list($color, $nb) = explode(' ', $sColorNb);
            $sBinaryPic .= str_repeat(intval($color !== 'W'), $nb);
        }
        $aBinaryPic = array_map('str_split', str_split($sBinaryPic, $this->w));
        array_unshift($aBinaryPic, null);
        return call_user_func_array('array_map', $aBinaryPic);
    }
    
    
    public function findColRef()
    {
        //Clean empty columns (left and right margins)
        foreach ($this->aPic as $nCol => $aCol) {
            if (array_sum($aCol) === 0) {
                unset($this->aPic[$nCol]);
            }
        }
        //The first col now contains only lines without notes. This will be a reference.
        $this->aColRef = reset($this->aPic);
    }
    
    public function findRefLines()
    {
        $flag = false;
        foreach ($this->aColRef as $hP => $vP) {
            if ($flag && $vP === '1') {
                ++$this->iThinLine;
            } elseif (!$flag && $vP === '1') {
                $this->aRefLines[] = $hP;
                ++$this->iThinLine;
                $flag = !$flag;
            } elseif ($flag && $vP === '0') {
                $flag = !$flag;
            }
        }
        $this->iThinLine = $this->iThinLine / count($this->aRefLines);
        
        //Add the aRefLine[5] for the lower "C" note.
        $this->aRefLines[5] = $this->aRefLines[4] + ($this->aRefLines[4] - $this->aRefLines[3]);
        $this->iGap = $this->aRefLines[1] - $this->aRefLines[0] - $this->iThinLine;
        
        //Add the colRef with the special line created for the "C" note
        $this->aColRefC = $this->aColRef;
        for ($i=0; $i<$this->iThinLine; ++$i) {
            $this->aColRefC[$this->aRefLines[5] + $i] = '1';
        }
    }
    
    public function read()
    {
        $aCurrentNote = [];
        $aDiff = [];
        $aColThatDiffIsOnlyTheCLine = range($this->aRefLines[5], $this->aRefLines[5] + $this->iThinLine - 1);
        foreach ($this->aPic as $nCol => $aCol) {
            
            //If the column is not the reference column, try to find the other ref col (with or without the special line)
            if ($aCol !== $this->aColRef) {
                $aDiff = array_diff_assoc($aCol, $this->aColRef);
                if ($aColThatDiffIsOnlyTheCLine !== array_keys($aDiff)) {
                    $aCurrentNote[] = $aDiff;
                    continue;
                }
            }
            
            //Here, the column is a reference column. This might be the end of a note (= $aCurrentNote is not empty) so let's
            //analyze the note we parsed
            if (!empty($aCurrentNote)) {
                //Take the longest note definition column
                $aAnalyzingNote = $this->_findNoteFromColumns($aCurrentNote);
                
                //And analyze this note using the reference lines.
                $sCurrentNote = $this->_analyzeNote($aAnalyzingNote);
                
                //Save the note in the array and reset the analizing current note to analyze the next one.
                $this->aNotes[] = $sCurrentNote;
                $aCurrentNote = [];
            }
            
            //Here, the column is a reference column but we weren't analyzing a note, so do nothing and continue.
        }
        
        return $this->aNotes;
    }
    
    private function _findNoteFromColumns($aCols) 
    {
        //Remove the draw of the "C" line if it is
        foreach ($aCols as $nCol => $aCol) {
            foreach ($aCol as $i => $index) {
                if ($this->aRefLines[5] <= $index && ($this->aRefLines[5] + $this->iThinLine) >= $index) {
                    unset($aCol[$i]);
                }
            }
            if (empty($aCol)) {
                unset($aCols[$nCol]);
            }
        }
        
        //Then, remove the firsts and lasts diffs because of the tail of the note.
        for ($i=0; $i<$this->iThinLine; ++$i) {
            array_shift($aCols);
            array_pop($aCols);
        }
        
        $aAnalyzingNote = $aCols[floor(count($aCols)/2)];
        return $aAnalyzingNote;
    }
    
    private function _analyzeNote($aNote)
    {
        $indexes = array_keys($aNote);
        $sPitch = $this->_findPitch($indexes);
        $sTempo = $this->_findTempo($indexes);
        
        return $sPitch . $sTempo;
    }
    
    private function _findPitch($indexes)
    {
        //If there's only one index because of thiners rounded notes, special case
        if (count($indexes) === 1) {
            return $this->_findPitchSepcial($indexes[0]);
        }
        
        list($highPoint, $lowPoint) = array(min($indexes), max($indexes));
        list($highMiddle, $lowMiddle) = array($highPoint+ceil($this->iGap/2), $lowPoint+ceil($this->iGap/2));
        
        if (
            $lowPoint <= $this->aRefLines[0] || //G1
            ($lowMiddle <= $this->aRefLines[4] && $highMiddle >= $this->aRefLines[3]) //G0
        ) {
            $sNote = 'G';
        } elseif(
            ($lowMiddle <= $this->aRefLines[1] && $highMiddle >= $this->aRefLines[0]) || //F1
            ($lowPoint <= $this->aRefLines[4] && $highPoint >= $this->aRefLines[3]) //F0
        ) {
            $sNote = 'F';
        } elseif(
            ($lowPoint <= $this->aRefLines[1] && $highPoint >= $this->aRefLines[0]) || //E1
            ($lowMiddle <= $this->aRefLines[5] && $highMiddle >= $this->aRefLines[4]) //E0
        ) {
            $sNote = 'E';
        } elseif(
            ($lowMiddle <= $this->aRefLines[2] && $highMiddle >= $this->aRefLines[1]) || //D1
            ($lowPoint <= ($this->aRefLines[5] + $this->iThinLine) && $highPoint >= $this->aRefLines[4]) //D0
        ) {
            $sNote = 'D';
        } elseif(
            ($lowPoint <= $this->aRefLines[2] && $highPoint >= $this->aRefLines[1]) || //C1
            ($lowPoint > ($this->aRefLines[5] + $this->iThinLine) && $highPoint < $this->aRefLines[4]) //C0
        ) {
            $sNote = 'C';
        } elseif($lowMiddle <= $this->aRefLines[3] && $highMiddle >= $this->aRefLines[2]) {
            $sNote = 'B';
        } elseif($lowPoint <= $this->aRefLines[3] && $highPoint >= $this->aRefLines[2]) {
            $sNote = 'A';
        } else {
            //If note note was not found, it's the default C0 note
            $sNote = 'C';
        }
        
        return $sNote;
    }
    
    private function _findPitchSepcial($index) 
    {
        //For this case, the note can't be on a line. It is positionned on a gap between lines.
        if ($index < $this->aRefLines[0]) {
            $sNote = 'G';
        } elseif($index < $this->aRefLines[1]) {
            $sNote = 'E';
        } elseif($index < $this->aRefLines[2]) {
            $sNote = 'C';
        } elseif($index < $this->aRefLines[3]) {
            $sNote = 'A';
        } elseif($index < $this->aRefLines[4]) {
            $sNote = 'F';
        } elseif($index < $this->aRefLines[5]) {
            $sNote = 'D';
        } else {
            $sNote = 'C';
        }
        
        return $sNote;
    }
    
    private function _findTempo($indexes)
    {
        //If there's only one index, it can only be a white note
        if (count($indexes) === 1) {
            return 'H';
        }
        
        $start = $indexes[0];
        //debug($indexes, $this->aRefLines[5]);
        for ($i=1, $n=count($indexes); $i<$n; ++$i) {
            if (++$start !== $indexes[$i]) {
                //If there's a hole in the note, maybe this hole is because of the line of the partition.
                //Let's try to fill this blank if it is.
                if (false === ($iRef = array_search($start, $this->aRefLines))) {
                    //If the blank wasn't for the line, it's a white note
                    return 'H';
                }
                //skip the blank part and continue
                $start += $this->iThinLine;
            }
        }
        
        //If there was no hole in the note, it's a black one, so let's display a quarter
        return 'Q';
    }
}

$oPartition = new Partition();
$aPic = $oPartition->aPic;
$oPartition->findColRef();
$oPartition->findRefLines();

$aNotes = $oPartition->read();

e(implode(' ', $aNotes));
?>