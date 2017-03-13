<?php
namespace ReceiptPrintHq\EscposTools\Parser;

use ReceiptPrintHq\EscposTools\Parser\Command\Printout;
use ReceiptPrintHq\EscposTools\Parser\Context\ParserContextImpl;

/**
 * API to parse files and iterate the results..
 *
 * Note that the actual parser has a character-only interface.
 */
class Parser
{
    protected $printout;

    public function __construct($profileName = "default")
    {
        $context = ParserContextImpl::byProfileName($profileName);
        $this -> printout = new Printout($context);
    }

    public function getCommands()
    {
        return $this -> printout -> commands;
    }

    public function addFile($fp)
    {
        while (!feof($fp) && is_resource($fp)) {
            $block = fread($fp, 8192);
            for ($i = 0; $i < strlen($block); $i++) {
                $this -> printout -> addChar($block[$i]);
            }
        }
    }
}
