<?php
namespace ReceiptPrintHq\EscposTools\Parser\Command;

use ReceiptPrintHq\EscposTools\Parser\Command\Command;

class FeedAndCutCmd extends Command implements LineBreak
{
    private $arg1 = null;
    private $arg2 = null;

    public function addChar($char)
    {
        if ($this -> arg1 === null) {
            $this -> arg1 = ord($char);
            return true;
        } elseif (in_array($this -> arg1, array(0, 48, 1, 49)) || $this -> arg2 !== null) {
            // One arg only, or arg already set
            return false;
        } else {
            // Read feed length also
            $this -> arg2 = ord($char);
            return true;
        }
    }
}
