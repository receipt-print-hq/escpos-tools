<?php
namespace ReceiptPrintHq\EscposTools\Parser\Command;

use ReceiptPrintHq\EscposTools\Parser\Command\LargeDataCmd;
use ReceiptPrintHq\EscposTools\Parser\Command\GraphicsDataCmd;

class GraphicsLargeDataCmd extends LargeDataCmd
{
    public function getSubCommand($m, $fn, $len)
    {
        // Same as regular graphics commands, just with more data!
        return GraphicsDataCmd::subCommandLookup($m, $fn, $len);
    }
}
