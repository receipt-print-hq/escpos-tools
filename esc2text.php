<?php
/**
 * Utility to extract text from binary ESC/POS data.
 */

interface LineBreak {}
 
interface TextContainer
{
    function getText();
}

interface ImageContainer
{
    function getWidth();
    
    function getHeight();
}

abstract class Command
{
    function __construct() 
    { 
    }
    
    function addChar($char) 
    {
        return false;
    }
}

abstract class EscposCommand extends Command
{
    private $stack;

    function __construct(array $stack) 
    {
        $this -> stack = $stack;
    }
}

class CommandOneArg extends EscposCommand
{
    private $arg = null;

    function addChar($char) 
    {
        if($this -> arg === null) {
            $this -> arg = ord($char);
            return true;
        } else {
            return false;
        }
    }
}

class CommandTwoArgs extends EscposCommand
{
    private $arg1 = null;
    private $arg2 = null;

    function addChar($char) 
    {
        if($this -> arg1 === null) {
            $this -> arg1 = ord($char);
            return true;
        } else if($this -> arg2 === null) {
            $this -> arg2 = ord($char);
            return true;
        }
        return false;
    }
}

class CommandThreeArgs extends EscposCommand
{
    private $arg1 = null;
    private $arg2 = null;
    private $arg3 = null;

    function addChar($char) 
    {
        if($this -> arg1 === null) {
            $this -> arg1 = ord($char);
            return true;
        } else if($this -> arg2 === null) {
            $this -> arg2 = ord($char);
            return true;
        } else if($this -> arg3 === null) {
            $this -> arg3 = ord($char);
            return true;
        }
        return false;
    }
}

class HorizontalTabCmd extends EscposCommand {}
class LineFeedCmd extends EscposCommand implements LineBreak {}
class FormFeedCmd extends EscposCommand {}
class CarriageReturnCmd extends EscposCommand {}
class InitializeCmd extends EscposCommand {}
class CancelCmd extends EscposCommand {}
class SelectPrintModeCmd extends CommandOneArg {}
class SelectCharacterSizeCmd extends CommandOneArg {}
class SelectPeripheralDeviceCmd extends CommandOneArg {}
class EnablePanelButtonsCmd extends CommandOneArg {}
class SelectCodeTableCmd extends CommandOneArg {}
class SelectPaperEndSensorsCmd extends CommandOneArg {}
class SelectPrintStopSensorsCmd extends CommandOneArg {}
class SelectInternationalCharacterSetCmd extends CommandOneArg {}
class EnableSmoothingCmd extends CommandOneArg {}
class EnableEmphasisCmd extends CommandOneArg {}
class EnableUnderlineCmd extends CommandOneArg {}
class EnableDoubleStrikeCmd extends CommandOneArg {}
class SelectDefaultLineSpacingCmd extends EscposCommand {}
class SelectFontCmd extends CommandOneArg {}
class SelectJustificationCmd extends CommandOneArg {}
class SelectLineSpacingCmd extends CommandOneArg {}
class PrintAndFeedCmd extends CommandOneArg implements LineBreak {}
class PrintAndFeedLinesCmd extends CommandOneArg implements LineBreak {}
class PrintAndReverseFeedLinesCmd extends CommandOneArg implements LineBreak {}
class SetAbsolutePrintPosCmd extends CommandTwoArgs {}
class UnknownCommandOneArg extends CommandOneArg {}
class SetBarcodeHeightCmd extends CommandOneArg {}
class SelectHriPrintPosCmd extends CommandOneArg {}
class PulseCmd extends CommandThreeArgs {}

class PrintBarcodeCmd extends EscposCommand
{
    private $m = null;
    private $subCommand = null;
    
    function addChar($char) 
    {
        if($this -> m === null) {
            $this -> m = ord($char);
            if((0 <= $this -> m) && ($this -> m <= 6)) {
                $this -> subCommand = new BarcodeAData();
            } else if((65 <= $this -> m) && ($this -> m <= 78)) {
                $this -> subCommand = new BarcodeBData();
            }
            return true;
        }
        if($this -> subCommand === null) {
            return false;
        }
        return $this -> subCommand -> addChar($char);
    }
}

class BarcodeAData extends Command
{
    private $data = "";
    private $done = false;

    function addChar($char) 
    {
        if($this -> done) {
            return false;
        }
        if($char == NUL) {
            $this -> done = true;
        } else {
            $this -> data .= $char;
        }
    }
}

class BarcodeBData extends Command
{
    private $data = "";
    private $len = null;

    function addChar($char) 
    {
        if($this -> len === null) {
            $this -> len = ord($char);
            return true;
        }
        if(strlen($this -> data) < $this -> len) {
            $this -> data .= $char;
            return true;
        }
        return false;   
    }
}

class PrintRasterBitImageCmd extends EscposCommand
{
    private $m = null;
    private $xL = null;
    private $xH = null;
    private $yL = null;
    private $yH = null;
    private $dataLen = null;
    private $data = "";

    function addChar($char) 
    {
        if($this -> dataLen !== null) {
            if(strlen($this -> data) < $this -> dataLen) {
                $this -> data .= $char;
                return true;
            }
            return false;            
        }
        if($this -> m === null) {
            $this -> m = ord($char);
            return true;
        }
        if($this -> xL === null) {
            $this -> xL = ord($char);
            return true;
        }
        if($this -> xH === null) {
            $this -> xH = ord($char);
            return true;
        }
        if($this -> yL === null) {
            $this -> yL = ord($char);
            return true;
        }
        if($this -> yH === null) {
            $this -> yH = ord($char);
            $this -> dataLen = ($this -> xL + $this -> xH * 256) * ($this -> yL + $this -> yH * 256);
            return true;
        }
        return false;
    }
}

abstract class DataCmd extends EscposCommand
{
    private $p1 = null;
    private $p2 = null;
    private $arg1 = null;
    private $arg2 = null;
    private $data = null;
    private $dataSize = null;
    private $subCommand = null;
    
    function addChar($char) 
    {
        if($this -> p1 === null) {
            $this -> p1 = ord($char);
            return true;
        } else if($this -> p2 === null) {
            $this -> p2 = ord($char);
            $this -> dataSize = $this -> p1 + $this -> p2 * 256;
            return true;
        } else if($this -> arg1 === null) {
            $this -> arg1 = ord($char);
            return true;
        } else if($this -> arg2 === null) {
            $this -> arg2 = ord($char);
            $this -> subCommand = $this -> getSubCommand($this -> arg1, $this -> arg2, $this -> dataSize - 2);            
            return true;
        }
        return $this -> subCommand -> addChar($char);
    }
    
    function getSubCommand($arg1, $arg2, $len) 
    {
        return new UnknownDataSubCmd($len);    
    }
}

class GraphicsDataCmd extends DataCmd { }
class Code2DDataCmd extends DataCmd { }
class RequestResponseTransmissionCmd extends DataCmd { }

class UnknownDataSubCmd extends Command
{
    private $data = "";
    private $dataSize;

    function __construct($dataSize) 
    {
        $this -> dataSize = $dataSize;
    }

    function addChar($char) 
    {
        if(strlen($this -> data) < $this -> dataSize) {
            $this -> data .= $char;
            return true;
        }
        return false;
    }
}

class FeedAndCutCmd extends Command
{
    private $arg1 = null;
    private $arg2 = null;

    function addChar($char) 
    {
        if($this -> arg1 === null) {
            $this -> arg1 = ord($char);
            return true;
        } else if(in_array($this -> arg1, array(0, 48, 1, 49)) || $this -> arg2 !== null) {
            // One arg only, or arg already set
            return false;
        } else {
            // Read feed length also
            $this -> arg2 = ord($char);
            return true;
        }
    }

}

class TextCmd extends Command implements TextContainer
{
    private $str = "";
    
    function addChar($char) 
    {
        if(isset(Printout::$tree[$char])) {
            // Reject ESC/POS control chars.
            return false;
        }
        $this -> str .= $char;
        return true;
    }
    
    function getText() 
    {
        return $this -> str;
    }
}

// Constants
const NUL = "\x00";
const HT = "\x09";
const LF = "\x0A";
const FF = "\x0C";
const CR = "\x0D";
const ESC = "\x1B";
const GS = "\x1D";
const FS = "\x1C";
const DLE = "\x10";
const CAN = "\x18";

// Top-level parser
class Printout extends Command
{
    public static $tree = array(
        HT => 'HorizontalTabCmd',
        LF => 'LineFeedCmd',
        FF => 'FormFeedCmd',
        CR => 'CarriageReturnCmd',
        ESC => array(
            '@' => 'InitializeCmd',
            '!' => 'SelectPrintModeCmd',
            '=' => 'SelectPeripheralDeviceCmd',
            'c' => array(
                '0' => 'UnknownCommandOneArg',
                '1' => 'UnknownCommandOneArg',
                '3' => 'SelectPaperEndSensorsCmd',
                '4' => 'SelectPrintStopSensorsCmd',
                '5' => 'EnablePanelButtonsCmd'
            ),
            '2' => 'SelectDefaultLineSpacingCmd',
            '3' => 'SelectLineSpacingCmd',
            'R' => 'SelectInternationalCharacterSetCmd',
            't' => 'SelectCodeTableCmd',
            'J' => 'PrintAndFeedCmd',
            '-' => 'EnableUnderlineCmd',
            'd' => 'PrintAndFeedLinesCmd',
            'G' => 'EnableDoubleStrikeCmd',
            'M' => 'SelectFontCmd',
            'a' => 'SelectJustificationCmd',
            'e' => 'PrintAndReverseFeedLinesCmd',
            '$' => 'SetAbsolutePrintPosCmd',
            'E' => 'EnableEmphasisCmd',
            'p' => 'PulseCmd'
        ),
        GS => array(
            '!' => 'SelectCharacterSizeCmd',
            'V' => 'FeedAndCutCmd',
            'b' => 'EnableSmoothingCmd',
            '(' => array(
                'C' => array(
                
                ),
                'E' => array(
                
                ),
                'H' => 'RequestResponseTransmissionCmd',
                'K' => array(
                
                ),
                'L' => 'GraphicsDataCmd',
                'k' => 'Code2dDataCmd'
            ),
            'h' => 'SetBarcodeHeightCmd',
            'H' => 'SelectHriPrintPosCmd',
            'k' => 'PrintBarcodeCmd',
            'v' => array(
                '0' => 'PrintRasterBitImageCmd'         
            )
        ),
        FS => array(

        ),
        DLE => array(

        ),
        CAN => 'CancelCmd'
    );

    public $commands = array();
    private $search;
    private $searchStack;

    function __construct() 
    {
        $this -> commands = array();
        $this -> reset();
    }

    function reset() 
    {
        $this -> search = null;
        $this -> searchStack = [];
    }

    function addChar($char) 
    {
        if(count($this -> searchStack) > 0) {
            // Matching parts of a command now.
            return $this -> navigateCommand($char);
        }

        if(count($this -> commands) != 0) {
            // Command is in progress
            $top = $this -> commands[count($this -> commands) - 1];
            $ret = $top -> addChar($char);
            if($ret) {
                // Character has been accepted by the command
                return true;
            }
        }
        // Has been rejected or we don't have a command yet. See if we can start a string
        if(count($this -> commands) == 0 || !is_a($this -> commands[count($this -> commands) - 1], 'TextCmd')) {
            $top = new TextCmd(array());
            if($top -> addChar($char)) {
                // Character was accepted to start a string.
                $this -> commands[] = $top;
                return true;
            }
        }
        // Character starts a command sequence
        $this -> search = self::$tree;
        return $this -> navigateCommand($char);
    }
    
    function navigateCommand($char) 
    {
        $this -> searchStack[] = $char;
        if(!isset($this -> search[$char])) {
            // Failed to match a command
            echo "WARNING: Unknown command " . implode($this -> searchStack) . "\n";
            $this -> reset();    
        } else if(is_array($this -> search[$char])) {
            // Command continues after this
            $this -> search = $this -> search[$char];
        } else {            
            // Matched a command right here
            $class = $this -> search[$char];
            $this -> commands[] = new $class($this -> searchStack);
            $this -> reset();
        }
    }
}

// Usage
if(!isset($argv[1])) {
    print("Usage: " . $argv[0] . " filename [-v]\n");
    die();
}

// Load in a file
$fp = fopen($argv[1], 'rb');

$printout = new Printout();
while (!feof($fp) && is_resource($fp)) {
    $block = fread($fp, 8192);
    for($i = 0; $i < strlen($block); $i++) {
        $printout -> addChar($block[$i]);
    }
}

// Extract text
foreach($printout -> commands as $cmd) {
    $impl = class_implements($cmd);
    if(isset($impl['TextContainer'])) {
        echo $cmd -> getText();
    }
    if(isset($impl['LineBreak'])) {
        echo "\n";
    }
}

if(isset($argv[2]) && $argv[2] == "-v") {
    // Print list of commands found
    print_r($printout);
}
