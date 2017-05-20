<?php
namespace ReceiptPrintHq\EscposTools\Parser\Context;

class InlineFormatting {
    public $bold = false;
    
    public function __construct() {

    }

    function setBold($bold) {
        $this -> bold = $bold;
    }

    public static function getDefault() {
        return new InlineFormatting();
    }
}
