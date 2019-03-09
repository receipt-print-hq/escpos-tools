# How to contribute

This project is open to many different types of contribution. You can help with improving the documentation and examples, sharing your insights on the issue tracker, adding fixes to the code, or providing test cases.

## Issue tracker

Open issues of all sorts are tracked on the [issue tracker](https://github.com/receipt-print-hq/escpos-tools/issues). Please check [the docs](https://github.com/receipt-print-hq/escpos-tools/blob/master/README.md) before you post, and practice good [bug tracker etiquette](https://bugzilla.mozilla.org/page.cgi?id=etiquette.html) to keep it running smoothly.

Issues are [loosely categorised](https://github.com/receipt-print-hq/escpos-tools/labels), and will stay open while there is still something that can be resolved.

Anybody may add to the discussion on the bug tracker. Just be sure to add new questions as separate issues, and to avoid commenting on closed issues.

## Submitting changes

Code changes may be submitted as a "[pull request](https://help.github.com/en/articles/about-pull-requests)" at [receipt-print-hq/escpos-tools](https://github.com/receipt-print-hq/escpos-tools). The description should include some information about how the change improves the library.

The project is MIT-licensed (see [LICENSE.md](https://github.com/receipt-print-hq/escpos-tools/blob/master/LICENSE.md) for details). You are not required to assign copyright in order to submit changes, but you do need to agree for your code to be distributed under this license in order for it to be accepted.

### Documentation changes

The official documentaton is also located in the main repository, under the [doc/](https://github.com/receipt-print-hq/escpos-tools/tree/master/doc) folder.

You are welcome to post any suggested improvements as pull requests.

### Release process

This project is still quite new, and does not have a formalised release process.

Changes should be submitted via pull request directly to the shared "master" branch.

## Code style

This project uses the [PSR-2 standard](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md) for all PHP source code.

## Testing and CI

The tests are executed on [Travis CI](https://travis-ci.org/receipt-print-hq/escpos-tools) over PHP 5.6 and 7.0. Earlier versions of PHP are not supported.

For development, you will require the `imagick` and `Xdebug` PHP exensions, the `composer` dependency manager, and acces to a `sass` compiler.

Fetch a copy of this code and load dependencies with composer:

    git clone https://github.com/receipt-print-hq/escpos-tools
    cd escpos-tools/
    composer install

Code style can be checked via [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer):

    php vendor/bin/phpcs --standard=psr2 src/ -n

The CI scripts currently just render a few receipts to check for obvious errors. You can find the commands to run locally in `travis.yml`.
