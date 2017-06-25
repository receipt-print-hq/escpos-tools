<?php
/**
 * Utility to extract images from binary ESC/POS data.
 */
require_once __DIR__ . '/vendor/autoload.php';

use ReceiptPrintHq\EscposTools\Parser\Parser;

// Usage
if (!isset($argv[1])) {
    print("Usage: " . $argv[0] . " filename(string) outputdir(string) outputpng(boolean)\n");
    die();
}
if (isset($argv[2]) && !is_dir($argv[2])) {
    print("Error: output dir must be a valid path if used\n");
    die();
}
if (isset($argv[3]) && !is_numeric($argv[3]) && $argv[3] > 1) {
    print("Error: outputpng must be 1 or 0 \n");
    die();
}
else
{
    $argv[3] = (bool)$argv[3];
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
            file_put_contents($argv[2]."img-$imgNo.pbm", $bufferedImg -> asPbm());
            if(isset($argv[3]) && $argv[3] == TRUE)
            {
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
