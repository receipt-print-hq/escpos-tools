<?php
namespace ReceiptPrintHq\EscposTools\Parser\Command;

use ReceiptPrintHq\EscposTools\Parser\Command\Command;

class UnknownDataSubCmd extends Command
{
    private $data = "";
    private $dataSize;

    function __construct($dataSize)
    {
        $this -> dataSize = $dataSize;
    }

    function addChar($char)
    {
        if (strlen($this -> data) < $this -> dataSize) {
            $this -> data .= $char;
            return true;
        }
        return false;
    }
}
