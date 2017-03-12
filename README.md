ESC/POS parser
--------------

This is a prototype ESC/POS parser, which can extract the commands contained in
printable ESC/POS binary data.

### esc2text utility
`esc2text` will simply extract text and line breaks. Add `-v` as the last argument to see all the
other commands being skipped over.

````
$ php esc2text.php receipt-with-logo.bin
ExampleMart Ltd.
Shop No. 42.

SALES INVOICE
                                               $
Example item #1                             4.00
Another thing                               3.50
Something else                              1.00
A final item                                4.45
Subtotal                                   12.95

A local tax                                 1.30
Total            $ 14.25

Thank you for shopping at ExampleMart
For trading hours, please visit example.com

Monday 6th of April 2015 02:56:25 PM
````

The same binary data can be sent to a printer:

````
$ cat receipt-with-logo.bin > /dev/usb/lp0 
````

![receipt-with-logo-small.png](https://raw.githubusercontent.com/mike42/escpos-snippets/master/parser/receipt-with-logo-small.png)

The input file is generated via [escpos-php](https://github.com/mike42/escpos-php).
