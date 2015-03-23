<?php
define('DEBUG', true);
function debug() {if (!DEBUG) return; foreach (func_get_args() as $sArgDebug) error_log(var_export($sArgDebug, true));}

class ParserCGX
{
    public $iCurrIndent = 0;
    public $bIsString = false;
    public $bNewLine = false;
    public $sCode = null;
    public $sOutput = null;
    
    public function __construct()
    {
        $sOneLineCode = '';
        fscanf(STDIN, "%d", $N);
        for ($i = 0; $i < $N; ++$i) {
            $sOneLineCode .= stream_get_line(STDIN, 1000, "\n");
        }
        $this->sCode = trim($sOneLineCode);
    }
    
    public function parse()
    {
        for ($i=0, $len=strlen($this->sCode); $i<$len; ++$i) {
            $char = $this->sCode[$i];
            debug($char);
            //Check if we are entering or leaving string mode
            if ($char === "'") {
                if ($this->_getPrevious() === "\n") {
                    $this->_addOutput($char);
                } else {
                    $this->sOutput .= $char;
                }
                $this->bIsString = !$this->bIsString;
                continue;
            }
            //If we're not, but we are in string mode, write the string.
            if ($this->bIsString) {
                $this->sOutput .= $char;
                continue;
            }
            
            //Otherwise, analyse
            if ($char === '(') {
                //If previous char was "=", assign a bloc so prefix by "\n"
                $prefix = ($this->_getPrevious() === '=') ? "\n" : '';
                $this->_addOutput("(\n", $prefix);
                $this->iCurrIndent += 4;
                continue;
            }
            if ($char === ')') {
                $this->iCurrIndent -= 4;
                $prefix = ($this->_getPrevious() !== "\n") ? "\n" : '';
                $this->_addOutput(')', $prefix);
                continue;
            }
            if ($char === ' ' || $char === "\n" || $char === "\r" || $char === "\t") {
                continue;
            }
            if ($char === ';') {
                $this->sOutput .= ";\n";
                continue;
            }
            
            if ($this->_getPrevious() === "\n") {
                $this->_addOutput($char);
            } else {
                $this->sOutput .= $char;
            }
            
        }
        
        return $this->sOutput;
    }
    
    protected function _addOutput($string, $prefix = '')
    {
        $this->sOutput .= $prefix . str_repeat(' ', $this->iCurrIndent) . $string;
    }
    
    protected function _getPrevious()
    {
        return substr($this->sOutput, -1);
    }
    
}

$oParser = new ParserCGX();
echo $oParser->parse();
?>