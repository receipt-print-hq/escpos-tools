<?php
namespace ReceiptPrintHq\EscposTools\Parser\Command;

use ReceiptPrintHq\EscposTools\Parser\Command\Command;

abstract class EscposCommand extends Command
{
    private $stack;

    function __construct(array $stack)
    {
        $this -> stack = $stack;
    }
}
