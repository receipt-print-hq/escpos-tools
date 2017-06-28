<?php
/**
 * Utility to extract images from binary ESC/POS data.
 */
require_once __DIR__ . '/vendor/autoload.php';

use ReceiptPrintHq\EscposTools\Parser\Parser;

$shortopts = "f:o:h";
$longopts  = array(
    "pbmonly"
);

$options = getopt($shortopts, $longopts);
if (empty($options) || array_key_exists("h", $options) || !array_key_exists("f", $options)) { 
    print "Usage " . $argv[0] . " -f '/path/to/binary/file' -o '/path/to/output/dir' --pbmonly\n";
    die(); 
}

// Load in a file
$fp = fopen($options['f'], 'rb');

$parser = new Parser();
$parser -> addFile($fp);

// Extract images
$bufferedImg = null;
$imgNo = 0;
$commands = $parser -> getCommands();
foreach ($commands as $cmd) {
    if ($cmd -> isAvailableAs('GraphicsDataCmd') || $cmd -> isAvailableAs('GraphicsLargeDataCmd')) {
        $sub = $cmd -> subCommand();
        if ($sub -> isAvailableAs('StoreRasterFmtDataToPrintBufferGraphicsSubCmd')) {
            $bufferedImg = $sub;
        } else if ($sub -> isAvailableAs('PrintBufferredDataGraphicsSubCmd')) {
            $desc = $bufferedImg -> getWidth() . 'x' . $bufferedImg -> getHeight();
            $imgNo = $imgNo + 1;
            echo "[ Image $imgNo: $desc ]\n";
            (array_key_exists("o", $options)) ? file_put_contents($options['o']."img-$imgNo.pbm", $bufferedImg -> asPbm()) : file_put_contents("img-$imgNo.pbm", $bufferedImg -> asPbm());
            if(!array_key_exists("pbmonly", $options)) {
                file_put_contents($argv[2]."img-$imgNo.png", $bufferedImg -> asPng());
            }
            $bufferedImg = null;
        }
    }
}

// Just for debugging
function shortName($longName)
{
    $nameParts = explode("\\", $longName);
    return array_pop($nameParts);
}
