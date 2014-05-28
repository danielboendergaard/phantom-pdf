<?php namespace PhantomPdf;

use Exception;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\Process\Process;

class PdfGenerator {

    /**
     * @var string
     */
    protected $baseUrl;

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
     * Create a PDF from a view or string
     * @param string $view
     * @param string $filename
     * @return BinaryFileResponse
     */
    public function createFromView($view, $filename)
    {
        $this->generateFilePaths();

        $this->generatePdf($view);

        return (new BinaryFileResponse($this->pdfPath))->setContentDisposition('attachment', $filename);
    }

    /**
     * Generate paths for the temporary files
     * @throws \Exception
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
        if (is_null($this->storagePath))
        {
            throw new Exception('A storage path has not been set');
        }

        if ( ! is_dir($this->storagePath) || ! is_writable($this->storagePath))
        {
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

        $options = implode(' ', $this->commandLineOptions);

        $command = __DIR__ . '/../bin/phantomjs '.$options.' generate-pdf.js '.$this->htmlPath.' '.$this->pdfPath;

        (new Process($command, __DIR__))->setTimeout($this->timeout)->run();
    }

    /**
     * Save a string to a html file
     * @param string $html
     */
    protected function saveHtml($html)
    {
        $html = $this->insertBaseTag($html);

        file_put_contents($this->htmlPath, (string) $html);
    }

    /**
     * Insert a base tag after the head tag to allow relative references to assets
     * @param string $view
     * @return string
     */
    protected function insertBaseTag($view)
    {
        if (is_null($this->baseUrl)) return $view;

        return str_replace('<head>', '<head><base href="'.$this->baseUrl.'">', $view);
    }

    /**
     * Delete temporary files
     */
    public function deleteTempFiles()
    {
        @unlink($this->htmlPath);

        @unlink($this->pdfPath);
    }

    /**
     * Set the base url for the base tag
     * @param string $url
     */
    public function setBaseUrl($url)
    {
        $this->baseUrl = $url;
    }

    /**
     * Set the storage path for temporary files
     * @param string $path
     */
    public function setStoragePath($path)
    {
        $this->storagePath = $path;
    }

    /**
     * @param int $seconds
     */
    public function setTimeout($seconds)
    {
        $this->timeout = $seconds;
    }

    /**
     * Ignore PhantomJS SSL errors
     */
    public function ignoreSSLErrors()
    {
        $this->commandLineOptions[] = '--ignore-ssl-errors=true';
    }
}