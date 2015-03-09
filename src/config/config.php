<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Temporary File Path
    |--------------------------------------------------------------------------
    |
    | The temporary file path where html and pdf files are stored.
    |
    */

    'temporary_file_path' => storage_path(),

    /*
    |--------------------------------------------------------------------------
    | The url for the base tag
    |--------------------------------------------------------------------------
    |
    | Since the HTML file that is loaded into PhantomJS is loaded from the file system,
    | any relative references to assets will not work. We fix this by adding a base tag
    | to the html file. If no value is set, the base url will be used.
    |
    */

    'base_url' => null,

    /*
    |--------------------------------------------------------------------------
    | Phantom Process Timeout (Seconds)
    |--------------------------------------------------------------------------
    |
    | PhantomJS is being executed in a separate process, here we can specify
    | how long to wait for the process to finish before aborting.
    |
    */

    'timeout' => 10,

    /*
    |--------------------------------------------------------------------------
    | Ignore SSL Errors (Deprecated - disabled in Laravel 5)
    |--------------------------------------------------------------------------
    |
    | Ignore SSL errors, such as expired or self-signed certificate errors.
    | Deprecated - add '--ignore-ssl-errors=true' to command line options instead
    |
    */

    'ignore_ssl_errors' => false,

    /*
    |--------------------------------------------------------------------------
    | PhantomJS Command Line Options
    |--------------------------------------------------------------------------
    |
    | Add list of wanted command line options for PhantomJS
    | List of available options can be found here:
    | http://phantomjs.org/api/command-line.html
    |
    */

    'command_line_options' => [
        //'--ignore-ssl-errors=true',
        //'--debug=true',
    ]

];