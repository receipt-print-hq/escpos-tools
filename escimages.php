<?php
/**
 * Utility to extract images from binary ESC/POS data.
 */
require_once __DIR__ . '/vendor/autoload.php';

use ReceiptPrintHq\EscposTools\Parser\Parser;

// Usage
if (!isset($argv[1])) {
    print("Usage: " . $argv[0] . " filename\n");
    die();
}

// Load in a file
$fp = fopen($argv[1], 'rb');

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
            file_put_contents("img-$imgNo.pbm", $bufferedImg -> asPbm());
            file_put_contents("img-$imgNo.png", $bufferedImg -> asPng());
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
