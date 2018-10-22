<?php
namespace ReceiptPrintHq\EscposTools\Parser\Command;

use ReceiptPrintHq\EscposTools\Parser\Command\Command;
use ReceiptPrintHq\EscposTools\Parser\Context\ParserContext;

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
            '*' => 'SelectBitImageModeCmd',
            '!' => 'SelectPrintModeCmd',
            '{' => 'EnableUpsideDownPrintModeCmd',
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
            'r' => 'SelectAlternateColorCmd',
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
            '\\' => 'SetRelativeVerticalPrintPositionCmd', // low and high values for vertrical print position (page mode)
            '!' => 'SelectCharacterSizeCmd',
            'V' => 'FeedAndCutCmd',
            'b' => 'EnableSmoothingCmd',
            'B' => 'EnableBlackWhiteInvertCmd',
            '(' => array(
                'C' => array(

                ),
                'E' => array(

                ),
                'H' => 'RequestResponseTransmissionCmd',
                'K' => array(

                ),
                'L' => 'GraphicsDataCmd',
                'k' => 'Code2DDataCmd',
                // Don't know what this command is, but could be a data command
                'J' => 'UnknownDataCmd',
            ),
            'I' => 'TransmitPrinterID',
            'h' => 'SetBarcodeHeightCmd',
            'w' => 'SetBarcodeWidthCmd',
            'H' => 'SelectHriPrintPosCmd',
            'k' => 'PrintBarcodeCmd',
            'v' => array(
                '0' => 'PrintRasterBitImageCmd'
            ),
            '8' => array(
                'L' => 'GraphicsLargeDataCmd'
            ),
            // Set horizontal and vertical motion units. args are x and y
            'P' => 'CommandTwoArgs'
        ),
        FS => array(
            '.' => 'CancelKanjiCharacterMode',
            'C' => 'SelectKanjiCharacterCode'
        ),
        DLE => array(

        ),
        CAN => 'CancelCmd'
    );

    public $commands = array();
    private $search;
    private $searchStack;

    public function __construct(ParserContext $context)
    {
        parent::__construct($context);
        $this -> commands = array();
        $this -> reset();
    }

    public function reset()
    {
        $this -> search = null;
        $this -> searchStack = array();
    }

    public function addChar($char)
    {
        if (count($this -> searchStack) > 0) {
            // Matching parts of a command now.
            return $this -> navigateCommand($char);
        }

        if (count($this -> commands) != 0) {
            // Command is in progress
            $top = $this -> commands[count($this -> commands) - 1];
            $ret = $top -> addChar($char);
            if ($ret) {
                // Character has been accepted by the command
                return true;
            }
        }
        // Has been rejected or we don't have a command yet. See if we can start a string
        if (count($this -> commands) == 0 || !is_a($this -> commands[count($this -> commands) - 1], 'TextCmd')) {
            $top = new TextCmd($this -> context, array());
            if ($top -> addChar($char)) {
                // Character was accepted to start a string.
                $this -> commands[] = $top;
                return true;
            }
        }
        // Character starts a command sequence
        $this -> search = self::$tree;
        return $this -> navigateCommand($char);
    }

    public function navigateCommand($char)
    {
        $this -> searchStack[] = $char;
        if (!isset($this -> search[$char])) {
            // Failed to match a command
            $this -> logUnknownCommand($this -> searchStack);
            $this -> reset();
        } elseif (is_array($this -> search[$char])) {
            // Command continues after this
            $this -> search = $this -> search[$char];
        } else {
            // Matched a command right here
            $class = "ReceiptPrintHq\\EscposTools\\Parser\\Command\\" . $this -> search[$char];
            $this -> commands[] = new $class($this -> context, $this -> searchStack);
            $this -> reset();
        }
    }
    
    public function logUnknownCommand(array $searchStack)
    {
        $nonPrintableMap = array(
            NUL => "NUL",
            HT => "HT",
            LF => "LF",
            FF => "FF",
            CR => "CR",
            ESC => "ESC",
            GS => "GS",
            FS => "FS",
            DLE => "DLE",
            CAN => "CAN"
        );
        $cmdStack = array();
        foreach ($searchStack as $s) {
            if (isset($nonPrintableMap[$s])) {
                $cmdStack[] = $nonPrintableMap[$s];
            } else {
                $cmdStack[] = $s;
            }
        }
        fwrite(STDERR, "WARNING: Unknown command " . implode($cmdStack, ' ') . "\n");
    }
}
