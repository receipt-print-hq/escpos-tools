<?php
namespace ReceiptPrintHq\EscposTools\Parser\Command;

use ReceiptPrintHq\EscposTools\Parser\Command\EscposCommand;

class PrintBarcodeCmd extends EscposCommand
{
    private $m = null;
    private $subCommand = null;

    function addChar($char)
    {
        if ($this -> m === null) {
            $this -> m = ord($char);
            if ((0 <= $this -> m) && ($this -> m <= 6)) {
                $this -> subCommand = new BarcodeAData();
            } elseif ((65 <= $this -> m) && ($this -> m <= 78)) {
                $this -> subCommand = new BarcodeBData();
            }
            return true;
        }
        if ($this -> subCommand === null) {
            return false;
        }
        return $this -> subCommand -> addChar($char);
    }
}
