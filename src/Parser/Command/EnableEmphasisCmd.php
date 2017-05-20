<?php
namespace ReceiptPrintHq\EscposTools\Parser\Command;

use ReceiptPrintHq\EscposTools\Parser\Command\CommandOneArg;
use ReceiptPrintHq\EscposTools\Parser\Command\InlineFormattingCmd;
use ReceiptPrintHq\EscposTools\Parser\Context\InlineFormatting;

class EnableEmphasisCmd extends CommandOneArg implements InlineFormattingCmd
{
    public function applyToInlineFormatting(InlineFormatting $formatting)
    {
        $formatting -> setBold($this -> getArg() == 1);
    }
}
