# esc2html

`esc2html` converts an ESC/POS binary file to a HTML document. It is currently capable of rendering unformatted ASCII text.

```
$ php esc2html.php receipt-with-logo.bin > output.html
```

## Installation

This utility is included with escpos-tools. See the
[escpos-tools documentation](https://github.com/receipt-print-hq/escpos-tools)
documentation for installation instructions.

## Usage

```
php esc2html FILE
```

## Example

TODO

## Further conversions

This utility will create a formatted HTML file. This can be converted accurately to PDF
via `wkhtmltopdf`, where formatted plaintext can be subsequently extracted via
`pdftotext -f -nopagebrk`, or images can be extracted via `pdfimages`. If some text
is embedded in the finished document as an image, then `pdfsandwich` can apply optical
character recognition to this.

Alternatively, the HTML may be converted accurately to a raster image via `wkhtmltoimage`.

The correct layout of the HTML document partially depends on the use of CSS3, so the
use of document converters such as `unoconv` and `pandoc` tends to lose some information.

These can can be used to produce RTF, DOC, and some other formats if the lossy conversion
is acceptable.

## See also

- [esc2text](esc2text.md)
- [escimages](escimages.md)

