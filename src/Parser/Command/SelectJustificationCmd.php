<?php
namespace ReceiptPrintHq\EscposTools\Parser\Command;

use ReceiptPrintHq\EscposTools\Parser\Command\CommandOneArg;
use ReceiptPrintHq\EscposTools\Parser\Command\InlineFormattingCmd;
use ReceiptPrintHq\EscposTools\Parser\Context\InlineFormatting;

class SelectJustificationCmd extends CommandOneArg implements InlineFormattingCmd
{
    public function applyToInlineFormatting(InlineFormatting $formatting)
    {
        $arg = $this -> getArg();
        if ($arg === 0 || $arg === 48) {
            $formatting -> setJustification(InlineFormatting::JUSTIFY_LEFT);
        } else if ($arg === 1 || $arg === 49) {
            $formatting -> setJustification(InlineFormatting::JUSTIFY_CENTER);
        } else if ($arg === 2 || $arg === 50) {
            $formatting -> setJustification(InlineFormatting::JUSTIFY_RIGHT);
        }
    }
}
