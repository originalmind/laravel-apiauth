<?php
/*
 * ApiAuth
 * A collection of filters to assist with protecting API routes.
 *
 * This package needs more work to be made generic.
 */

namespace OriginalMind\ApiAuth;

use Illuminate\Support\ServiceProvider;

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
	}

	private function attachFilters() {
		$this->app['router']->filter('apiauthuserowner', 'apiauth.filter.userowner');
	}
}
