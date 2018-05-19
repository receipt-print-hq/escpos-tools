<?php
namespace ReceiptPrintHq\EscposTools\Parser\Command;

use ReceiptPrintHq\EscposTools\Parser\Command\DataCmd;
use Imagick;

class SelectBitImageModeCmd extends EscposCommand implements ImageContainer
{

    private $m = null;

    private $p1 = null;

    private $p2 = null;

    private $data = "";

    private $dataSize = null;

    public function addChar($char)
    {
        if ($this->m === null) {
            $this->m = ord($char);
            return true;
        } else if ($this->p1 === null) {
            $this->p1 = ord($char);
            return true;
        } elseif ($this->p2 === null) {
            $this->p2 = ord($char);
            $this->width = $this->p1 + $this->p2 * 256;
            if ($this->m == 32 || $this->m == 33) {
                $this->dataSize = $this->width * 3;
                $this->height = 24;
            } else {
                $this->dataSize = $this->width;
                $this->height = 8;
            }
            return true;
        } else if (strlen($this->data) < $this->dataSize) {
            $this->data .= $char;
            return true;
        }
        return false;
    }

    public function getHeight()
    {
        return $this -> height;
    }

    public function getWidth()
    {
        return $this -> width;
    }
    
    protected function asReflectedPbm()
    {
        // Gemerate a PBM image from the source data. If we add a PBM header to the column
        // format ESC/POS data with the width and height swapped, then we get a valid PBM, with
        // the image reflected diagonally compared with the original.
        return "P4\n" . $this -> getHeight() . " " . $this -> getWidth() . "\n" . $this -> data;
    }
    
    public function asPbm()
    {
        // Reflect image diagonally from internally generated PBM
        $pbmBlob = $this -> asReflectedPbm();
        $im = new Imagick();
        $im -> readImageBlob($pbmBlob, 'pbm');
        $im -> rotateImage('#fff', 90.0);
        $im -> flopImage();
        return $im -> getImageBlob();
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
