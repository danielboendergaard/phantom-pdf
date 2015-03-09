<?php namespace PhantomPdf\Laravel;

use Illuminate\Support\ServiceProvider;
use PhantomPdf\PdfGenerator;

class PhantomPdfServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('danielboendergaard/phantom-pdf', 'phantom-pdf', __DIR__.'/..');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		//phantom-pdf
        $this->app->bind('phantom-pdf', function()
        {
            $generator = new PdfGenerator;

            $generator->setBaseUrl($this->app['config']['phantom-pdf::base_url'] ?: url());

            $generator->setStoragePath($this->app['config']['phantom-pdf::temporary_file_path']);

            $generator->setTimeout($this->app['config']['phantom-pdf::timeout']);

            if ($this->app['config']['phantom-pdf::ignore_ssl_errors']) {
                $generator->ignoreSSLErrors();
            }

            foreach ($this->app['config']['phantom-pdf::command_line_options'] as $option)
            {
                $generator->addCommandLineOption($option);
            }

            $this->app->finish(function() use ($generator) {
                $generator->deleteTempFiles();
            });

           return $generator;
        });
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return ['phantom-pdf'];
	}

}
