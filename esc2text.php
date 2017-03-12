<?php
/**
 * Utility to extract text from binary ESC/POS data.
 */
require_once __DIR__ . '/vendor/autoload.php';

use ReceiptPrintHq\EscposTools\Parser\Command\Printout;

// Usage
if (!isset($argv[1])) {
    print("Usage: " . $argv[0] . " filename [-v]\n");
    die();
}

// Load in a file
$fp = fopen($argv[1], 'rb');

$printout = new Printout();
while (!feof($fp) && is_resource($fp)) {
    $block = fread($fp, 8192);
    for ($i = 0; $i < strlen($block); $i++) {
        $printout -> addChar($block[$i]);
    }
}

// Extract text
foreach ($printout -> commands as $cmd) {
    $impl = class_implements($cmd);
    if (isset($impl['ReceiptPrintHq\\EscposTools\\Parser\\Command\\TextContainer'])) {
        echo $cmd -> getText();
    }
    if (isset($impl['ReceiptPrintHq\\EscposTools\\Parser\\Command\\LineBreak'])) {
        echo "\n";
    }
}

if (isset($argv[2]) && $argv[2] == "-v") {
    // Print list of commands found
    print_r($printout);
}
