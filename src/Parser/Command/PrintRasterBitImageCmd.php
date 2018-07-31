<?php
namespace ReceiptPrintHq\EscposTools\Parser\Command;

use ReceiptPrintHq\EscposTools\Parser\Command\EscposCommand;
use Imagick;

class PrintRasterBitImageCmd extends EscposCommand implements ImageContainer
{
    private $m = null;
    private $xL = null;
    private $xH = null;
    private $yL = null;
    private $yH = null;
    private $width = null;
    private $height = null;
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
            $this -> width = $this -> xL + $this -> xH * 256;
            $this -> height = $this -> yL + $this -> yH * 256;
            $this -> dataLen = $this -> width * $this -> height;
            return true;
        }
        return false;
    }
    public function getHeight()
    {
        return $this -> height;
    }

    public function asPbm()
    {
        return "P4\n" . $this -> getWidth() . " " . $this -> getHeight() . "\n" . $this -> data;
    }

    public function getWidth()
    {
        return $this -> width * 8;
    }

    public function asPng()
    {
        // Just a format conversion PBM -> PNG
        $pbmBlob = $this -> asPbm();
        $im = new Imagick();
        $im -> readImageBlob($pbmBlob, 'pbm');
        $im->setResourceLimit(6, 1); // Prevent libgomp1 segfaults, grumble grumble.
        $im -> setFormat('png');
        return $im -> getImageBlob();
    }
}
