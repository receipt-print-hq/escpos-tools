<?php
namespace ReceiptPrintHq\EscposTools\Parser\Command;

use ReceiptPrintHq\EscposTools\Parser\Command\Command;
use ReceiptPrintHq\EscposTools\Parser\Command\DataSubCmd;
use \Imagick;

class StoreRasterFmtDataToPrintBufferGraphicsSubCmd extends DataSubCmd implements ImageContainer
{
    private $tone = null;
    private $color = null;
    
    private $widthMultiple = null;
    private $heightMultiple = null;
    
    private $x1 = null;
    private $x2 = null;
    private $y1 = null;
    private $y2 = null;
    
    private $data = "";
    private $dataSize;

    public function __construct($dataSize)
    {
        $this -> dataSize = $dataSize - 8;
    }

    public function addChar($char)
    {
        if ($this -> tone == null) {
            $this -> tone = ord($char);
            return true;
        } else if ($this -> color === null) {
            $this -> color = ord($char);
            return true;
        } else if ($this -> widthMultiple === null) {
            $this -> widthMultiple = ord($char);
            return true;
        } else if ($this -> heightMultiple === null) {
            $this -> heightMultiple = ord($char);
            return true;
        } else if ($this -> x1 === null) {
            $this -> x1 = ord($char);
            return true;
        } else if ($this -> x2 === null) {
            $this -> x2 = ord($char);
            return true;
        } else if ($this -> y1 === null) {
            $this -> y1 = ord($char);
            return true;
        } else if ($this -> y2 === null) {
            $this -> y2 = ord($char);
            return true;
        } else if (strlen($this -> data) < $this -> dataSize) {
            $this -> data .= $char;
            return true;
        }
        return false;
    }

    public function getWidth()
    {
        return $this -> x1 + $this -> x2 * 256;
    }
    
    public function getHeight()
    {
        return $this -> y1 + $this -> y2 * 256;
    }
    
    public function asPbm()
    {
        return "P4\n" . $this -> getWidth() . " " . $this -> getHeight() . "\n" . $this -> data;
    }
    
    public function asPng()
    {
        $pbmBlob = $this -> asPbm();
        $im = new Imagick();
        $im -> readImageBlob($pbmBlob, 'pbm');
        $im->setResourceLimit(6, 1); // Prevent libgomp1 segfaults, grumble grumble.
        $im -> setFormat('png');
        return $im -> getImageBlob();
    }
}
