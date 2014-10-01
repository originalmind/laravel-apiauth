<?php
namespace OriginalMind\ApiAuth;

use Exception;
use Illuminate\Support\ServiceProvider;

/**
 * ApiAuthServiceProvider
 *
 * A collection of filters to assist with protecting API routes.
 *
 * @package OriginalMind\ApiAuth
 * @since 0.1.0
 */
class ApiAuthServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Name of oauth2 resource server in Laravel IoC container.
	 *
	 * @var string
	 */
	protected $resourceServerName = "oauth2-server.authorizer";

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
			return new Filters\ApiAuthUserOwnerFilter([$this, 'OAuthOwnerIdCallback']);
		});
	}

	/**
	 * Function to be provided to filter. Retrieves access token owner ID from resource server.
	 *
	 * @return int
	 * @since 0.1.0
	 */
	public function OAuthOwnerIdCallback() {
		$resourceServer = \App::make($this->resourceServerName);

		// We can't compare the user credentials to the oauth token.
		if ($resourceServer === null) {
			throw new Exception("Unable to instantiate resourceServer");
		}

		try {
			$resourceServer->validateAccessToken();
		} catch (Exception $e) {
			throw new Exception(
				"No access token is present in this request.",
				0, $e);
		}

		$accessTokenOwnerId = $resourceServer->getChecker()->getOwnerId();
		\Log::info("Access token owner id", [$accessTokenOwnerId]);

		return $accessTokenOwnerId;
	}

	private function attachFilters() {
		$this->app['router']->filter('apiauthuserowner', 'apiauth.filter.userowner');
	}
}
