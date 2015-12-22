<?php

namespace PhantomPdf;

use Exception;
use Symfony\Component\Process\Exception\RuntimeException;
use Symfony\Component\Process\Process;

class PdfGenerator
{
    /**
     * @var string
     */
    protected $baseUrl;

    /**
     * @var string
     */
    protected $binaryPath = 'phantomjs';

    /**
     * @var string
     */
    protected $storagePath;

    /**
     * @var string
     */
    protected $htmlPath;

    /**
     * @var string
     */
    protected $pdfPath;

    /**
     * @var int
     */
    protected $timeout = 10;

    /**
     * @var array
     */
    protected $commandLineOptions = [];

    /**
     * @var string
     */
    protected $convertScript = 'generate-pdf.js';

    /**
     * @var string
     */
    protected $orientation = 'portrait';

    /**
     * Save a PDF file to the disk
     * @param string|object $view
     * @param string $path
     */
    public function saveFromView($view, $path)
    {
        $this->generateFilePaths();

        $this->generatePdf($this->viewToString($view));

        rename($this->pdfPath, $path);
    }

    /**
     * Generate paths for the temporary files
     * @throws Exception
     */
    protected function generateFilePaths()
    {
        $this->validateStoragePath();

        $path = $this->storagePath . DIRECTORY_SEPARATOR;

        $this->htmlPath = $path . uniqid('pdf-', true).'.html';

        $this->pdfPath = $path . uniqid('html-', true) . '.pdf';
    }

    /**
     * Validate that the storage path is set and is writable
     * @throws \Exception
     */
    protected function validateStoragePath()
    {
        if (is_null($this->storagePath)) {
            throw new Exception('A storage path has not been set');
        }

        if (! is_dir($this->storagePath) || ! is_writable($this->storagePath)) {
            throw new Exception('The specified storage path is not writable');
        }
    }

    /**
     * Run the script with PhantomJS
     * @param string $view
     */
    protected function generatePdf($view)
    {
        $this->saveHtml($view);

        $command = implode(' ', [
            $this->binaryPath,
            implode(' ', $this->commandLineOptions),
            $this->convertScript,
            $this->prefixHtmlPath($this->htmlPath),
            $this->pdfPath,
            $this->orientation
        ]);

        $process = new Process($command, __DIR__);
        $process->setTimeout($this->timeout);
        $process->run();

        if ($errorOutput = $process->getErrorOutput()) {
            throw new RuntimeException('PhantomJS: ' . $errorOutput);
        }

        // Remove temporary html file
        if (is_file($this->htmlPath)) {
            unlink($this->htmlPath);
        }
    }

    /**
     * Convert the provided view to a string. The __toString method is called manually to be able to catch exceptions
     * in the view which is not possible otherwise. https://bugs.php.net/bug.php?id=53648
     * @param mixed $view
     * @return string
     */
    protected function viewToString($view)
    {
        return is_object($view) ? $view->__toString() : $view;
    }

    /**
     * Save a string to a html file
     * @param string $html
     */
    protected function saveHtml($html)
    {
        $html = $this->insertBaseTag($html);

        file_put_contents($this->htmlPath, $html);
    }

    /**
     * Insert a base tag after the head tag to allow relative references to assets
     * @param string $view
     * @return string
     */
    protected function insertBaseTag($view)
    {
        if (is_null($this->baseUrl)) {
            return $view;
        }

        return str_replace('<head>', '<head><base href="'.$this->baseUrl.'">', $view);
    }

    /**
     * Prefix the input path for windows versions of PhantomJS
     * @param string $path
     * @return string
     */
    public function prefixHtmlPath($path)
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            return 'file:///' . $path;
        }

        return $path;
    }

    /**
     * Set the base url for the base tag
     * @param string $url
     * @return self
     */
    public function setBaseUrl($url)
    {
        $this->baseUrl = $url;

        return $this;
    }

    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * Set the binary path
     * @param string $path
     * @return self
     */
    public function setBinaryPath($path)
    {
        $this->binaryPath = $path;

        return $this;
    }

    public function getBinaryPath()
    {
        return $this->binaryPath;
    }

    /**
     * Set the storage path for temporary files
     * @param string $path
     * @return self
     */
    public function setStoragePath($path)
    {
        $this->storagePath = $path;

        return $this;
    }

    public function getStoragePath()
    {
        return $this->storagePath;
    }

    /**
     * @param  int $seconds
     * @return self
     */
    public function setTimeout($seconds)
    {
        $this->timeout = (int) $seconds;

        return $this;
    }

    /**
     * @return int
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * Ignore PhantomJS SSL errors
     * @return self
     */
    public function ignoreSSLErrors()
    {
        $this->commandLineOptions[] = '--ignore-ssl-errors=true';

        return $this;
    }

    /**
     * Add a command line option for PhantomJS
     * @param string $option
     * @return self
     */
    public function addCommandLineOption($option)
    {
        $this->commandLineOptions[] = $option;

        return $this;
    }

    /**
     * @return array
     */
    public function getCommandLineOptions()
    {
        return $this->commandLineOptions;
    }

    /**
     * Use a custom script to be run via PhantomJS
     * @param string $path
     * @return self
     */
    public function setConvertScript($path)
    {
        $this->convertScript = $path;

        return $this;
    }

    /**
     * @return string
     */
    public function getConvertScript()
    {
        return $this->convertScript;
    }

    /**
     * @return string
     */
    public function getOrientation()
    {
        return $this->orientation;
    }

    /**
     * @param string $orientation
     * @return self
     */
    public function setOrientation($orientation)
    {
        $this->orientation = $orientation;

        return $this;
    }
}
