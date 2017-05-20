<?php
namespace ReceiptPrintHq\EscposTools\Parser\Command;
use ReceiptPrintHq\EscposTools\Parser\Context\InlineFormatting;

interface InlineFormattingCmd {
    function applyToInlineFormatting(InlineFormatting $formatting);
}
