<?php
namespace ReceiptPrintHq\EscposTools\Parser\Command;

use ReceiptPrintHq\EscposTools\Parser\Command\Command;
use ReceiptPrintHq\EscposTools\Parser\Context\ParserContext;

abstract class EscposCommand extends Command
{
    protected $stack;

    public function __construct(ParserContext $context, array $stack)
    {
        parent::__construct($context);
        $this -> stack = $stack;
    }
}
