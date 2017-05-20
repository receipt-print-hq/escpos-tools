<?php
/**
 * Utility to convert binary ESC/POS data to HTML
 */
require_once __DIR__ . '/vendor/autoload.php';

use ReceiptPrintHq\EscposTools\Parser\Parser;
use ReceiptPrintHq\EscposTools\Parser\Context\InlineFormatting;

// Usage
if (!isset($argv[1])) {
    print("Usage: " . $argv[0] . " filename\n");
    die();
}

// Load in a file
$fp = fopen($argv[1], 'rb');

$parser = new Parser();
$parser -> addFile($fp);

// Extract text
$commands = $parser -> getCommands();
$formatting = InlineFormatting::getDefault();
$outp = array();
$lineHtml = "";
foreach ($commands as $cmd) {
    if ($cmd -> isAvailableAs('InitializeCmd')) {
        $formatting = InlineFormatting::getDefault();
    }
    if ($cmd -> isAvailableAs('InlineFormattingCmd')) {
        $cmd -> applyToInlineFormatting($formatting);
    }
    if ($cmd -> isAvailableAs('TextContainer')) {
        // Add text to line
        $spanContentHtml = htmlentities($cmd -> getText());
        $lineHtml .= span($formatting, $spanContentHtml);
    }
    if ($cmd -> isAvailableAs('LineBreak')) {
        // Write fresh block element out to HTML
        if ($lineHtml === "") {
            $lineHtml = span($formatting, "&nbsp;");
        }
        $outp[] = wrapInline("<div class=\"esc-line\">", "</div>", $lineHtml);
        $lineHtml = "";
    }
}

// Stuff we need in the HTML header
const CSS_FILE = __DIR__ . "/src/resources/esc2html.css";
$metaInfo = array_merge(
    array(
        "<meta charset=\"UTF-8\">",
        "<style>"
    ),
    explode("\n", trim(file_get_contents(CSS_FILE))),
    array(
        "</style>"
    )
);

// Final document assembly
$receipt = wrapBlock("<div class=\"esc-receipt\">", "</div>", $outp);
$head = wrapBlock("<head>", "</head>", $metaInfo);
$body = wrapBlock("<body>", "</body>", $receipt);
$html = wrapBlock("<html>", "</html>", array_merge($head, $body), false);
echo "<!DOCTYPE html>\n" . implode("\n", $html) . "\n";


function wrapInline($tag, $closeTag, $content)
{
    return $tag . $content . $closeTag;
}

function wrapBlock($tag, $closeTag, array $content, $indent = true)
{
    $ret = array();
    $ret[] = $tag;
    foreach ($content as $line) {
        $ret[] = ($indent ? '  ' : '') . $line;
    }
    $ret[] = $closeTag;
    return $ret;
}

function span(InlineFormatting $formatting, $text) {
    $classes = [];

    if($formatting -> bold) {
        $classes[] = "esc-emphasis";
    }
    
    // Output span with any non-default classes
    if(count($classes) == 0) {
        return $text;
    }
    return "<span class=\"". implode($classes, " ") . "\">" . $text . "</span>";
}
