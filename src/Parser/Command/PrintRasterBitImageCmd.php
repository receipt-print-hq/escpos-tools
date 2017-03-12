<?php
namespace ReceiptPrintHq\EscposTools\Parser\Command;

use ReceiptPrintHq\EscposTools\Parser\Command\EscposCommand;

class PrintRasterBitImageCmd extends EscposCommand
{
    private $m = null;
    private $xL = null;
    private $xH = null;
    private $yL = null;
    private $yH = null;
    private $dataLen = null;
    private $data = "";

    public function addChar($char)
    {
        if ($this -> dataLen !== null) {
            if (strlen($this -> data) < $this -> dataLen) {
                $this -> data .= $char;
                return true;
            }
            return false;
        }
        if ($this -> m === null) {
            $this -> m = ord($char);
            return true;
        }
        if ($this -> xL === null) {
            $this -> xL = ord($char);
            return true;
        }
        if ($this -> xH === null) {
            $this -> xH = ord($char);
            return true;
        }
        if ($this -> yL === null) {
            $this -> yL = ord($char);
            return true;
        }
        if ($this -> yH === null) {
            $this -> yH = ord($char);
            $this -> dataLen = ($this -> xL + $this -> xH * 256) * ($this -> yL + $this -> yH * 256);
            return true;
        }
        return false;
    }
}
