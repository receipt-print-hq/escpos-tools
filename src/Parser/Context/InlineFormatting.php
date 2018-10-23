<?php
namespace ReceiptPrintHq\EscposTools\Parser\Context;

use Mike42\Escpos\Printer;

class InlineFormatting
{
    const JUSTIFY_LEFT = 0;
    const JUSTIFY_CENTER = 1;
    const JUSTIFY_RIGHT = 2;
    
    const FONT_A = 0;
    const FONT_B = 1;
    const FONT_C = 2;

    public $bold;
    public $widthMultiple;
    public $heightMultiple;
    public $justification;
    public $underline;
    public $invert;
    public $font;
    public $upsideDown;

    public function __construct()
    {
        $this -> reset();
    }

    public function setBold($bold)
    {
        $this -> bold = $bold;
    }
    
    public function setInvert($invert)
    {
        $this -> invert = $invert;
    }

    public function setWidthMultiple($width)
    {
        $this -> widthMultiple = $width;
    }
    
    public function setHeightMultiple($height)
    {
        $this -> heightMultiple = $height;
    }

    public function setFont($font)
    {
        $this -> font = $font;
    }

    public function setJustification($justification)
    {
        $this -> justification = $justification;
    }

    public function setUnderline($underline)
    {
        $this -> underline = $underline;
    }

    public function setUpsideDown($upsideDown)
    {
        $this -> upsideDown = $upsideDown;
    }

    public static function getDefault()
    {
        return new InlineFormatting();
    }

    public function reset()
    {
        $this -> bold = false;
        $this -> widthMultiple = 1;
        $this -> heightMultiple = 1;
        $this -> justification = InlineFormatting::JUSTIFY_LEFT;
        $this -> underline = 0;
        $this -> invert = false;
        $this -> font = 0;
        $this -> upsideDown = false;
    }
}
