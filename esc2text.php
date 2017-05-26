<?php
/**
 * Utility to extract text from binary ESC/POS data.
 */
require_once __DIR__ . '/vendor/autoload.php';

use ReceiptPrintHq\EscposTools\Parser\Parser;

// Usage
if (!isset($argv[1])) {
    print("Usage: " . $argv[0] . " filename [-v]\n");
    die();
}
$debug = isset($argv[2]) && $argv[2] == "-v";

// Load in a file
$fp = fopen($argv[1], 'rb');

$parser = new Parser();
$parser -> addFile($fp);

// Extract text
$commands = $parser -> getCommands();
foreach ($commands as $cmd) {
    if ($debug) {
        // Debug output if requested. List commands and the interface for retrieving the data.
        $className = shortName(get_class($cmd));
        $impl = class_implements($cmd);
        foreach ($impl as $key => $val) {
            $impl[$key] = shortName($val);
        }
        $implStr = count($impl) == 0 ? "" : "(" . implode(", ", $impl) . ")";
        fwrite(STDERR, "[DEBUG] $className {$implStr}\n");
    }
    if ($cmd -> isAvailableAs('TextContainer')) {
        echo $cmd -> getText();
    }
    if ($cmd -> isAvailableAs('LineBreak')) {
        echo "\n";
    }
}

// Just for debugging
function shortName($longName)
{
    $nameParts = explode("\\", $longName);
    return array_pop($nameParts);
}
