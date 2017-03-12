<?php
namespace ReceiptPrintHq\EscposTools\Parser\Command;

use ReceiptPrintHq\EscposTools\Parser\Command\Command;

class BarcodeBData extends Command
{
    private $data = "";
    private $len = null;

    public function addChar($char)
    {
        if ($this -> len === null) {
            $this -> len = ord($char);
            return true;
        }
        if (strlen($this -> data) < $this -> len) {
            $this -> data .= $char;
            return true;
        }
        return false;
    }
}
