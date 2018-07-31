<?php
/**
 * Utility to extract images from binary ESC/POS data.
 */
require_once __DIR__ . '/vendor/autoload.php';

use ReceiptPrintHq\EscposTools\Parser\Parser;
use ReceiptPrintHq\EscposTools\Parser\Command\ImageContainer;

function outpImg($outputDir, $imgNo, ImageContainer $img, $outputPbm, $outputPng, $receiptName)
{
    // Output an image
    $desc = $img -> getWidth() . 'x' . $img -> getHeight();
    echo "[ Image $imgNo: $desc ]\n";
    $outpFilename = $outputDir . '/' . "$receiptName-" . sprintf('%02d', $imgNo);
    if ($outputPbm) {
        file_put_contents($outpFilename . ".pbm", $img -> asPbm());
    }
    if ($outputPng) {
        file_put_contents($outpFilename . ".png", $img -> asPng());
    }
}

// Read CLI options in
$shortopts = "f:o:h";
$longopts  = array(
    "file:",
    "output-dir:",
    "png",
    "pbm",
    "help"
);
$options = getopt($shortopts, $longopts);
$usage = "Usage: " . $argv[0] . " OPTIONS --file 'filename'\n";
// Input file
$inputFile = null;
$inputFile = array_key_exists("f", $options) ? $options["f"] : $inputFile;
$inputFile = array_key_exists("file", $options) ? $options["file"] : $inputFile;
// Output dir
$outputDir = ".";
$outputDir = array_key_exists("o", $options) ? $options["o"] : $outputDir;
$outputDir = array_key_exists("output-dir", $options) ? $options["output-dir"] : $outputDir;
// Help
$showHelp = array_key_exists("h", $options) || array_key_exists("help", $options);
// Output formats
$outputPng = array_key_exists("png", $options);
$outputPbm = array_key_exists("pbm", $options);
if (!$outputPng && !$outputPbm) {
  // Default
    $outputPng = true;
}

if (empty($options) || ( $inputFile === null && !$showHelp)) {
  // Incorrect usage shows error and quits nonzero
    $error = "$usage\nTry '" . $argv[0] . " --help' for more information.\n";
    file_put_contents("php://stderr", $error);
    exit(1);
}
if (array_key_exists("h", $options) || array_key_exists("help", $options)) {
  // Request for help
    $message = "$usage
 Required options:

  -f, --file FILE             The input file to read.

 Output options:

  -o, --output-dir DIRECTORY  The directory to write output files to.

 Output format:

  Select one or more formats for output. If none is specified, then PNG is used.

  --png                        Write output files in PNG format.
  --pbm                        Write output files in PBM format.

 Other options:
  -h, --help                   Show this help\n";
    echo $message;
    exit(0);
}

// Quick validation
if (!file_exists($outputDir) || !is_dir($outputDir)) {
    $error = "Output location does not exist, or is not a directory.\n";
    file_put_contents("php://stderr", $error);
    exit(1);
}
$outputDir = rtrim($outputDir, '/');
if (!file_exists($inputFile) || !is_readable($inputFile)) {
    $error = "Input file does not exist, or is not readable.\n";
    file_put_contents("php://stderr", $error);
    exit(1);
}
$receiptName = $path_parts = pathinfo($inputFile)['filename'];

// Load in a file
$fp = @fopen($inputFile, 'rb');
if (!$fp) {
    $error = "Failed to open the input file\n";
    file_put_contents("php://stderr", $error);
    exit(1);
}

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
            // Assign image when stored
            $bufferedImg = $sub;
        } else if ($sub -> isAvailableAs('PrintBufferredDataGraphicsSubCmd')) {
            // Print assigned image
            $imgNo = $imgNo + 1;
            outpImg($outputDir, $imgNo, $bufferedImg, $outputPbm, $outputPng, $receiptName);
            $bufferedImg = null;
        }
    } else if ($cmd -> isAvailableAs('ImageContainer')) {
        $imgNo = $imgNo + 1;
        outpImg($outputDir, $imgNo, $cmd, $outputPbm, $outputPng, $receiptName);
    }
}

// Just for debugging
function shortName($longName)
{
    $nameParts = explode("\\", $longName);
    return array_pop($nameParts);
}
