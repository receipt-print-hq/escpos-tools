<?php
namespace ReceiptPrintHq\EscposTools\Parser\Command;

use ReceiptPrintHq\EscposTools\Parser\Context\ParserContext;

abstract class Command
{
    protected $context;

    public function __construct(ParserContext $context)
    {
        $this -> context = $context;
    }

    public function addChar($char)
    {
        return false;
    }

    public function isAvailableAs($interface)
    {
        $className = get_called_class();
        if ($className == "ReceiptPrintHq\\EscposTools\\Parser\\Command\\$interface") {
            return true;
        }
        $impl = class_implements($this);
        return isset($impl["ReceiptPrintHq\\EscposTools\\Parser\\Command\\$interface"]);
    }
}
