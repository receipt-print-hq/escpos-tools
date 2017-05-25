<?php
namespace ReceiptPrintHq\EscposTools\Parser\Command;

use ReceiptPrintHq\EscposTools\Parser\Command\DataCmd;

class GraphicsDataCmd extends DataCmd
{
    public function getSubCommand($m, $fn, $len)
    {
        // Delegate to static function so that GraphicsLargeDataCmd can use the same list
        return GraphicsDataCmd::subCommandLookup($m, $fn, $len);
    }
    
    public static function subCommandLookup($m, $fn, $len)
    {
        if ($fn === 0 || $fn === 48) {
            return new UnknownDataSubCmd($len);
        } else if ($fn === 1 || $fn === 49) {
            return new UnknownDataSubCmd($len);
        } else if ($fn === 2 || $fn === 50) {
            return new PrintBufferredDataGraphicsSubCmd($len);
        } else if ($fn === 3 || $fn === 51) {
            return new UnknownDataSubCmd($len);
        } else if ($fn === 4 || $fn === 52) {
            return new UnknownDataSubCmd($len);
        } else if ($fn === 64) {
            return new UnknownDataSubCmd($len);
        } else if ($fn === 65) {
            return new UnknownDataSubCmd($len);
        } else if ($fn === 66) {
            return new UnknownDataSubCmd($len);
        } else if ($fn === 67) {
            return new UnknownDataSubCmd($len);
        } else if ($fn === 68) {
            return new UnknownDataSubCmd($len);
        } else if ($fn === 69) {
            return new UnknownDataSubCmd($len);
        } else if ($fn === 80) {
            return new UnknownDataSubCmd($len);
        } else if ($fn === 81) {
            return new UnknownDataSubCmd($len);
        } else if ($fn === 82) {
            return new UnknownDataSubCmd($len);
        } else if ($fn === 83) {
            return new UnknownDataSubCmd($len);
        } else if ($fn === 84) {
            return new UnknownDataSubCmd($len);
        } else if ($fn === 85) {
            return new UnknownDataSubCmd($len);
        } else if ($fn === 112) {
            // Raster format data to print buffer
            return new StoreRasterFmtDataToPrintBufferGraphicsSubCmd($len);
        } else if ($fn === 113) {
            // Column format data to print buffer
            return new StoreColumnFmtDataToPrintBufferGraphicsSubCmd($len);
        }
        // Fallthrough for unknown sub-commands.
        return new UnknownDataSubCmd($len);
    }
}
