<?php
namespace OriginalMind\ApiAuth\Filters;

use Log;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * ApiAuthUserOwnerFilter
 *
 * This filter is for use with Controllers orchestrating the display/modification of Users (or
 * a custom User implementation.)
 * Checks that oauth token owner ID matches the ID of the user account being displayed/modified.
 *
 * @package OriginalMind\ApiAuth\Filters
 * @since 0.1.0
 */
class ApiAuthUserOwnerFilter {

	protected $resourceServerIdCallback;

	/**
	 * Create the filter, specifying the method to retrieve the access token's owner ID from the
	 * oauth resource server.
	 *
	 * @param function resourceServerIdCallback (provides a way to retrieve the owern ID
	 * of the access token)
	 * @since 0.1.0
	 */
	public function __construct($resourceServerIdCallback) {
		$this->resourceServerIdCallback = $resourceServerIdCallback;
	}

	/**
	 * Route filter - throws HttpException (403) if access token owner ID does not match the User
	 * entity ID.
	 *
	 * <code>
	 * <?php
	 * $this->beforeFilter('apiauthuserowner:account', array('only' => array('show', 'update')));
	 * ?>
	 * </code>
	 *
	 * @param Route $route
	 * @param Request $request
	 * @param string $modelName (Used to find the entity ID in the route parameters.)
	 * @return void
	 * @since 0.1.0
	 */
	public function filter($route, $request, $modelName) {

		Log::info("Running ApiAuthUserOwnerFilter");

		$entityId = -1;

		if ($route !== null) {
			$entityId = $route->parameter($modelName);
			Log::info("model name, entity id", [$modelName, $entityId]);
		}

		if ($entityId === -1) {
			Log::error("Unable to retrieve entity id by model name:", [$modelName]);
			return;
		}

		$accessTokenOwnerId = call_user_func($this->resourceServerIdCallback);

		Log::info("Access token owner id (returned from callback)", [$accessTokenOwnerId]);

		if ($entityId !== $accessTokenOwnerId) {
			throw new HttpException(403,
				"Your access token is not valid for this operation");
		}

	}
}
