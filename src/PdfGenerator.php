<?php namespace PhantomPdf;

use Exception;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\Process\Exception\RuntimeException;
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
     * @param string|object $view
     * @param string $filename
     * @return BinaryFileResponse
     */
    public function createFromView($view, $filename)
    {
        $this->generateFilePaths();

        $this->generatePdf($view);

        $response = (new BinaryFileResponse($this->pdfPath))
            ->setContentDisposition('attachment', $filename);

        if (method_exists($response, 'deleteFileAfterSend')) {
            $response->deleteFileAfterSend(true);
        }

        return $response;
    }

    /**
     * Save a PDF file to the disk
     * @param string|object $view
     * @param string $path
     */
    public function saveFromView($view, $path)
    {
        $this->generateFilePaths();

        $this->generatePdf($view);

        rename($this->pdfPath, $path);
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
        if (is_null($this->storagePath)) {
            throw new Exception('A storage path has not been set');
        }

        if ( ! is_dir($this->storagePath) || ! is_writable($this->storagePath)) {
            throw new Exception('The specified storage path is not writable');
        }
    }

    /**
     * Run the script with PhantomJS
     * @param string $view
     */
    protected function generatePdf($view)
    {
        $view = $this->viewToString($view);

        $this->saveHtml($view);

        $options = implode(' ', $this->commandLineOptions);

        $command = __DIR__ . '/../bin/phantomjs '.$options.' generate-pdf.js '.$this->htmlPath.' '.$this->pdfPath;

        $process = new Process($command, __DIR__);
        $process->setTimeout($this->timeout);
        $process->run();

        if ($errorOutput = $process->getErrorOutput()) {
            throw new RuntimeException('PhantomJS: ' . $errorOutput);
        }

        // Remove temporary html file
        @unlink($this->htmlPath);
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
        if (is_null($this->baseUrl)) return $view;

        return str_replace('<head>', '<head><base href="'.$this->baseUrl.'">', $view);
    }

    /**
     * Delete temporary files
     */
    public function deleteTempFiles()
    {
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

    /**
     * Add a command line option for PhantomJS
     * @param string $option
     */
    public function addCommandLineOption($option)
    {
        $this->commandLineOptions[] = $option;
    }
}