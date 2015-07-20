Phantom PDF
===========

A Package for generating PDF files using PhantomJS. The package is framework agnostic, but provides integration with Laravel 4/5.

Notice: This package only ships with the 64-bit Linux version of PhantomJS. If you want to use it with another version you can reference it in the configuration.

##Installation
Run `composer require danielboendergaard/phantom-pdf`

####Laravel 4 Installation (optional)

Add `PhantomPdfServiceProvider` in the `providers` array in `app/config/app.php`
````
'providers' => [
  ...
  'PhantomPdf\Laravel\PhantomPdfServiceProvider'
]
````

####Laravel 5 Installation (optional)

Add `Laravel5ServiceProvider` in the `providers` array in `config/app.php`
````
'providers' => [
  ...
  'PhantomPdf\Laravel\Laravel5ServiceProvider'
]
````

#### Laravel 4/5 Facade usage (optional)

Add the facade to the `aliases` array in `app/config/app.php` (optional)
````
'aliases' => [
  ...
  'PDF' => 'PhantomPdf\Laravel\PDFFacade'
]
````

##Usage with Laravel
````php
class SampleController extends Controller {

  public function index()
  {
    $view = View::make('index');
    
    return PDF::createFromView($view, 'filename.pdf');
  }

  public function save()
  {
      $view = View::make('index');

      PDF::saveFromView($view, 'path/filename.pdf');
  }
}
````

##General usage

````php

$pdf = new PdfGenerator;

// Set a writable path for temporary files
$pdf->setStoragePath('storage/path');

// Saves the PDF as a file
$pdf->saveFromView($html, 'filename.pdf');

// Returns a Symfony\Component\HttpFoundation\BinaryFileResponse
return $pdf->createFromView($html, 'filename.pdf');

````

Use `setBinaryPath('path')` to use another version of PhantomJS.

###Customizing the conversion script
If you want to use another script to execute with PhantomJS, this it how you do it.
````php
$pdf->useScript('path/to/script');

return $pdf->saveFromView('view');
````
