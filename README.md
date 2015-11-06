Phantom PDF
===========

[![Build Status](https://travis-ci.org/clippings/phantom-pdf.png?branch=master)](https://travis-ci.org/clippings/phantom-pdf)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/clippings/phantom-pdf/badges/quality-score.png?s=a1404674f68c4894d651150caf4985aa59597515)](https://scrutinizer-ci.com/g/clippings/phantom-pdf/)
[![Code Coverage](https://scrutinizer-ci.com/g/clippings/phantom-pdf/badges/coverage.png?s=3d5fb55c42c6887679915320658b543ed935e00a)](https://scrutinizer-ci.com/g/clippings/phantom-pdf/)
[![Latest Stable Version](https://poser.pugx.org/clippings/phantom-pdf/v/stable.png)](https://packagist.org/packages/clippings/phantom-pdf)

Installation
------------

Install via composer

```
$ composer global require clippings/composer-init
```

Usage
-----

````php
$pdf = new PdfGenerator();

// Set a writable path for temporary files
$pdf->setStoragePath('storage/path');

// Saves the PDF as a file
$pdf->saveFromView($html, 'filename.pdf');
````

Use `setBinaryPath('path')` to use another version of PhantomJS.

Customizing the conversion script
---------------------------------

If you want to use another script to execute with PhantomJS, this it how you do it.
````php
$pdf->useScript('path/to/script');

return $pdf->saveFromView('view');
````

Credits
-------

Forked from the great https://github.com/danielboendergaard/phantom-pdf package

Copyright (c) 2015, Clippings Ltd. Refactored by [Ivan Kerin](https://github.com/ivank) as part of [clippings.com](http://clippings.com)
