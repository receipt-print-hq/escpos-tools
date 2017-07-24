<?php
/**
 * Utility to extract images from binary ESC/POS data.
 */
require_once __DIR__ . '/vendor/autoload.php';

use ReceiptPrintHq\EscposTools\Parser\Parser;

// Read CLI options in
$shortopts = "f:o:h";
$longopts  = array(
    "file:",
    "output-dir:",
    "png",
    "pbm",
    "help",
    "include-blank-lines"
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
// Include blank lines
$blankLines = array_key_exists("include-blank-lines", $options);

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
  --include-blank-lines       Output a 24px tall, 1px wide image for each line of text encountered.

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
            $bufferedImg = $sub;
        } else if ($sub -> isAvailableAs('PrintBufferredDataGraphicsSubCmd')) {
            $desc = $bufferedImg -> getWidth() . 'x' . $bufferedImg -> getHeight();
            $imgNo = $imgNo + 1;
            echo "[ Image $imgNo: $desc ]\n";
            $outpFilename = $outputDir . '/' . "$receiptName-" . sprintf('%02d', $imgNo);
            if ($outputPbm) {
                file_put_contents($outpFilename . ".pbm", $bufferedImg -> asPbm());
            }
            if ($outputPng) {
                file_put_contents($outpFilename . ".png", $bufferedImg -> asPng());
            }
            $bufferedImg = null;
        }
    }
    if ($blankLines) {
        if ($cmd -> isAvailableAs('LineBreak')) {
            $imgNo = $imgNo + 1;
            $outpFilename = $outputDir . '/' . "$receiptName-" . sprintf('%02d', $imgNo);
            echo "[ Image $imgNo: 1x24 ]\n";
            if ($outputPbm) {
                $data = "P4\n1 24\n 0000 0000 0000 0000 0000 0000";
                file_put_contents($outpFilename.'.pbm',$data);
            }
            if ($outputPng) {
                ob_start();
                $png_image = imagecreate(1, 24);
                imagecolorallocate($png_image, 255, 255, 255);
                imagepng($png_image);
                $data = ob_get_clean();
                imagedestroy($png_image);
                file_put_contents($outpFilename.'.png',$data);
            }
        }
    }
}

// Just for debugging
function shortName($longName)
{
    $nameParts = explode("\\", $longName);
    return array_pop($nameParts);
}
