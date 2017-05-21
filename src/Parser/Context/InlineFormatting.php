<?php
namespace ReceiptPrintHq\EscposTools\Parser\Context;

use Mike42\Escpos\Printer;

class InlineFormatting
{
    const JUSTIFY_LEFT = 0;
    const JUSTIFY_CENTER = 1;
    const JUSTIFY_RIGHT = 2;
    
    public $bold = false;
    
    public $widthMultiple = 1;
    public $heightMultiple = 1;

    public $justification = InlineFormatting::JUSTIFY_LEFT;

    public function __construct()
    {
    }

    public function setBold($bold)
    {
        $this -> bold = $bold;
    }
    
    public function setWidthMultiple($width)
    {
        $this -> widthMultiple = $width;
    }
    
    public function setHeightMultiple($height)
    {
        $this -> heightMultiple = $height;
    }

    public function setJustification($justification)
    {
        $this -> justification = $justification;
    }

    public static function getDefault()
    {
        return new InlineFormatting();
    }
}
