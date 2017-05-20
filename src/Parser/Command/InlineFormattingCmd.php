<?php
namespace ReceiptPrintHq\EscposTools\Parser\Command;

use ReceiptPrintHq\EscposTools\Parser\Context\InlineFormatting;

interface InlineFormattingCmd
{
    public function applyToInlineFormatting(InlineFormatting $formatting);
}
