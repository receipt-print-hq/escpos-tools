<?php
namespace ReceiptPrintHq\EscposTools\Parser\Command;

use ReceiptPrintHq\EscposTools\Parser\Command\EscposCommand;
use ReceiptPrintHq\EscposTools\Parser\Command\InlineFormattingCmd;
use ReceiptPrintHq\EscposTools\Parser\Context\InlineFormatting;

class InitializeCmd extends EscposCommand implements InlineFormattingCmd
{
    public function applyToInlineFormatting(InlineFormatting $formatting)
    {
        $formatting -> reset();
    }
}
