<?php

namespace PhantomPdf\Test;

use PHPUnit_Framework_TestCase;
use PhantomPdf\PdfGenerator;
use Exception;
use DirectoryIterator;
use Symfony\Component\Process\Exception\RuntimeException;
use Symfony\Component\Process\Process;

/**
 * @coversDefaultClass PhantomPdf\PdfGenerator
 */
class PdfGeneratorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    private $files;

    /**
     * @var string
     */
    private $testHtml;

    public function setUp()
    {
        parent::setUp();

        $this->files = __DIR__.'/../files/';

        foreach (new DirectoryIterator($this->files) as $fileInfo) {
            if ('.' !== $fileInfo->getFilename()[0]) {
                unlink($fileInfo->getPathname());
            }
        }

        $this->testHtml = file_get_contents(__DIR__.'/../test.html');
    }

    /**
     * @covers ::setBaseUrl
     * @covers ::getBaseUrl
     * @covers ::setBinaryPath
     * @covers ::getBinaryPath
     * @covers ::setStoragePath
     * @covers ::getStoragePath
     * @covers ::setTimeout
     * @covers ::getTimeout
     * @covers ::ignoreSSLErrors
     * @covers ::addCommandLineOption
     * @covers ::getCommandLineOptions
     * @covers ::setConvertScript
     * @covers ::getConvertScript
     */
    public function testConstruct()
    {
        $timeout = 20;
        $storagePath = 'some-dir';
        $script = 'otherScript.js';
        $baseUrl = 'https://example.com/test';
        $phantomjs = '/var/bin/phantomjs';

        $generator = new PdfGenerator();
        $generator
            ->setStoragePath($storagePath)
            ->setTimeout($timeout)
            ->setBaseUrl($baseUrl)
            ->setConvertScript($script)
            ->ignoreSSLErrors()
            ->setBinaryPath($phantomjs)
            ->addCommandLineOption('--other-option=Test');

        $this->assertEquals(
            $storagePath,
            $generator->getStoragePath(),
            'Should save storage path'
        );

        $this->assertEquals(
            $timeout,
            $generator->getTimeout(),
            'Should save timeout'
        );

        $this->assertEquals(
            $baseUrl,
            $generator->getBaseUrl(),
            'Should save base url'
        );

        $this->assertEquals(
            $script,
            $generator->getConvertScript(),
            'Should save converter script'
        );

        $this->assertEquals(
            $phantomjs,
            $generator->getBinaryPath(),
            'Should save binary path'
        );

        $this->assertEquals(
            [
                '--ignore-ssl-errors=true',
                '--other-option=Test',
            ],
            $generator->getCommandLineOptions(),
            'Should combine data from ignoreSSLErrors and addCommandLineOption'
        );
    }

    /**
     * @covers ::saveFromView
     * @covers ::generateFilePaths
     * @covers ::validateStoragePath
     * @covers ::generatePdf
     * @covers ::viewToString
     * @covers ::saveHtml
     * @covers ::insertBaseTag
     * @covers ::deleteTempFiles
     * @covers ::prefixHtmlPath
     */
    public function testPdfGeneration()
    {
        $generator = new PdfGenerator();
        $generator->setStoragePath($this->files);

        $generator->saveFromView($this->testHtml, $this->files.'converted-test.pdf');

        $this->assertFileExists($this->files.'converted-test.pdf');
    }

    /**
     * @covers ::insertBaseTag
     * @covers ::deleteTempFiles
     */
    public function testBaseTag()
    {
        $generator = new PdfGenerator();
        $generator
            ->setStoragePath($this->files)
            ->setBaseUrl('http://example.com/test');

        $generator->saveFromView($this->testHtml, $this->files.'converted-test2.pdf');
        $generator->deleteTempFiles();

        $this->assertFileExists($this->files.'converted-test2.pdf');
    }

    /**
     * @covers ::generatePdf
     */
    public function testException()
    {
        $generator = new PdfGenerator();
        $generator
            ->setStoragePath($this->files)
            ->setBinaryPath('phantomjs-not-found');

        $this->setExpectedException(
            'Symfony\Component\Process\Exception\RuntimeException',
            'PhantomJS: sh: '
        );

        $generator->saveFromView('test', $this->files.'no.pdf');
    }

    /**
     * @covers ::generatePdf
     */
    public function testNoStoragePath()
    {
        $generator = new PdfGenerator();

        $this->setExpectedException(
            'Exception',
            'A storage path has not been set'
        );

        $generator->saveFromView('test', $this->files.'no.pdf');
    }

    /**
     * @covers ::generatePdf
     */
    public function testBadStoragePath()
    {
        $generator = new PdfGenerator();
        $generator
            ->setStoragePath(__DIR__.'/no-storage-path');

        $this->setExpectedException(
            'Exception',
            'The specified storage path is not writable'
        );

        $generator->saveFromView('test', $this->files.'no.pdf');
    }
}
