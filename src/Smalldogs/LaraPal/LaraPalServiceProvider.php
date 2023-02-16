<?php namespace Smalldogs\LaraPal;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;

class LaraPalServiceProvider extends ServiceProvider {

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
		$this->package('smalldogs/larapal');

		AliasLoader::getInstance()->alias('LaraPal', 'Smalldogs\LaraPal\LaraPal');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->bindShared('larapal', function ()
		{
			return $this->app->make('Smalldogs\LaraPal\LaraPal');
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

}
