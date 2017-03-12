<?php
namespace ReceiptPrintHq\EscposTools\Parser\Command;

abstract class Command
{
    function __construct()
    {
    }

    function addChar($char)
    {
        return false;
    }
}
