<?php
namespace ReceiptPrintHq\EscposTools\Parser\Command;

use ReceiptPrintHq\EscposTools\Parser\Command\CommandOneArg;
use ReceiptPrintHq\EscposTools\Parser\Command\InlineFormattingCmd;
use ReceiptPrintHq\EscposTools\Parser\Context\InlineFormatting;

class SelectPrintModeCmd extends CommandOneArg implements InlineFormattingCmd
{
    public function applyToInlineFormatting(InlineFormatting $formatting)
    {
        $arg = $this -> getArg();
        // TODO Add font A/B selection from this command (1)
        $formatting -> setBold($arg & 8);
        $formatting -> setHeightMultiple($arg & 16 ? 2 : 1);
        $formatting -> setWidthMultiple($arg & 32 ? 2 : 1);
        // TODO Add underline text option from this command (128)
    }
}
