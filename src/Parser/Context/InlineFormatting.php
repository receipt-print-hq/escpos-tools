<?php
namespace ReceiptPrintHq\EscposTools\Parser\Context;

class InlineFormatting
{
    public $bold = false;
    
    public $widthMultiple = 1;
    public $heightMultiple = 1;
    
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

    public static function getDefault()
    {
        return new InlineFormatting();
    }
}
