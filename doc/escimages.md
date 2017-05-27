# escimages

`escimages` extracts raster images from binary ESC/POS files.

All non-graphical elements of the receipt are ignored, and a pair of files is
written in PBM and PNG format for each image found in the source file.

## Installation

This utility is included with escpos-tools. See the
[escpos-tools documentation](https://github.com/receipt-print-hq/escpos-tools)
documentation for installation instructions.

## Usage

```
php escimages.php FILE
```

## Example

TODO

## Further conversions

This utility will create raster images only. They can be printed back to a printer
via `escpos-php`, or rendered to other formats with the ImageMagick `convert` utility.

In some cases, drivers convert text to a series of image before printing. To recover
text from this type of receipt, the images from `escimage` can be stacked and
sent through a suitable OCR tool to extract the text.

For example, using `tesseract`:

```
rm *.pbm
php escimages.php input.bin
convert -append *.pbm pbm:- | tesseract - -
```

## See also

- [esc2html](esc2html.md)
- [esc2text](esc2text.md)

