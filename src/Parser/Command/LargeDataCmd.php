<?php
namespace ReceiptPrintHq\EscposTools\Parser\Command;

use ReceiptPrintHq\EscposTools\Parser\Command\EscposCommand;

abstract class LargeDataCmd extends EscposCommand
{
    private $p1 = null;
    private $p2 = null;
    private $p3 = null;
    private $p4 = null;
    private $arg1 = null;
    private $arg2 = null;
    private $data = null;
    private $dataSize = null;
    private $subCommand = null;
    
    public function addChar($char)
    {
        if ($this -> p1 === null) {
            $this -> p1 = ord($char);
            return true;
        } elseif ($this -> p2 === null) {
            $this -> p2 = ord($char);
            return true;
        } elseif ($this -> p3 === null) {
            $this -> p3 = ord($char);
            return true;
        } elseif ($this -> p4 === null) {
            $this -> p4 = ord($char);
            $this -> dataSize = $this -> p1 + $this -> p2 * 256 + $this -> p3 * 65536 + $this -> p4 * 16777216;
            return true;
        } elseif ($this -> arg1 === null) {
            $this -> arg1 = ord($char);
            return true;
        } elseif ($this -> arg2 === null) {
            $this -> arg2 = ord($char);
            $this -> subCommand = $this -> getSubCommand($this -> arg1, $this -> arg2, $this -> dataSize - 2);
            return true;
        }
        return $this -> subCommand -> addChar($char);
    }
    
    public function getSubCommand($arg1, $arg2, $len)
    {
        return new UnknownDataSubCmd($len);
    }
    
    public function subCommand()
    {
        // TODO rename and take getSubCommand() name.
        return $this -> subCommand;
    }
}
