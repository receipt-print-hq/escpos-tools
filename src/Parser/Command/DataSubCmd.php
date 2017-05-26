<?php
namespace ReceiptPrintHq\EscposTools\Parser\Command;

use ReceiptPrintHq\EscposTools\Parser\Command\Command;

class DataSubCmd extends Command
{
    private $data = "";
    private $dataSize;

    public function __construct($dataSize)
    {
        $this -> dataSize = $dataSize;
    }

    public function addChar($char)
    {
        if (strlen($this -> data) < $this -> dataSize) {
            $this -> data .= $char;
            return true;
        }
        return false;
    }
}
