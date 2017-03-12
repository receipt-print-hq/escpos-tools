<?php
namespace ReceiptPrintHq\EscposTools\Parser\Command;

use ReceiptPrintHq\EscposTools\Parser\Command\EscposCommand;

class CommandThreeArgs extends EscposCommand
{
    private $arg1 = null;
    private $arg2 = null;
    private $arg3 = null;

    public function addChar($char)
    {
        if ($this -> arg1 === null) {
            $this -> arg1 = ord($char);
            return true;
        } elseif ($this -> arg2 === null) {
            $this -> arg2 = ord($char);
            return true;
        } elseif ($this -> arg3 === null) {
            $this -> arg3 = ord($char);
            return true;
        }
        return false;
    }
}
