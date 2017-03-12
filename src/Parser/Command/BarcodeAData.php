<?php
namespace ReceiptPrintHq\EscposTools\Parser\Command;

use ReceiptPrintHq\EscposTools\Parser\Command\Command;

class BarcodeAData extends Command
{
    private $data = "";
    private $done = false;

    public function addChar($char)
    {
        if ($this -> done) {
            return false;
        }
        if ($char == NUL) {
            $this -> done = true;
        } else {
            $this -> data .= $char;
        }
    }
}
