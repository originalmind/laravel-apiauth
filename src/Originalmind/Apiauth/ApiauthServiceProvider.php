<?php

namespace OriginalMind\ApiAuth;

use Illuminate\Support\ServiceProvider;

// use Illuminate\Foundation\AliasLoader;
// use Originalmind\Apiauth\Filters\ApiAuthFilter;
// use LucaDegasperi\OAuth2Server\Facades\ResourceServerFacade;

class ApiAuthServiceProvider extends ServiceProvider {

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
	public function boot() {
		$this->package('originalmind/apiauth');

		// include __DIR__ . '/../../routes.php';

		// include __DIR__ . '/Filters/ApiAuthFilter.php';

		// Create our own alias
		// $loader = AliasLoader::getInstance();
		// $loader->alias('ResourceServer', 'LucaDegasperi\OAuth2Server\Facades\ResourceServerFacade');

		$this->attachFilters();
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register() {
		$this->registerFilters();
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides() {
		return ['apiauth.filter.userowner'];
	}

	private function registerFilters() {

		$this->app['apiauth.filter.userowner'] = $this->app->share(function ($app) {
			return new Filters\ApiAuthUserOwnerFilter('oauth2-server.authorizer');
		});

		// $this->app->bindShared('Originalmind\Apiauth\Filters\ApiAuthFilter', function ($app) {
		// 	return new ApiAuthFilter('oauth2-server.authorizer');
		// });
	}

	private function attachFilters() {
		// $this->app['router']->filter('apiauth', 'Originalmind\Apiauth\Filters\ApiAuthFilter');
		$this->app['router']->filter('apiauthuserowner', 'apiauth.filter.userowner');
		// $this->app['apiauth.filter']);
		// 'Originalmind\Apiauth\Filters\ApiAuthFilter');
	}
}
