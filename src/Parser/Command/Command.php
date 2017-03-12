<?php
namespace ReceiptPrintHq\EscposTools\Parser\Command;

abstract class Command
{
    public function __construct()
    {
    }

    public function addChar($char)
    {
        return false;
    }
}
