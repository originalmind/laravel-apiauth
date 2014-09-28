<?php
/*
 * ApiAuthUserOwnerFilter
 * This filter is for use with Controllers orchestrating the display/modification of Users (or
 * a custom User implementation.)
 * Checks that oauth token owner ID matches the ID of the user account being displayed/modified.
 */

namespace OriginalMind\ApiAuth\Filters;

use App;
use Log;
use Symfony\Component\HttpKernel\Exception\HttpException;
use \Exception;

class ApiAuthUserOwnerFilter {

	protected $resourceServerName;

	public function __construct($resourceServerName) {
		$this->resourceServerName = $resourceServerName;
	}

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

		$resourceServer = App::make($this->resourceServerName);

		// We can't compare the user credentials to the oauth token.
		if ($resourceServer === null) {
			Log::error("Unable to instantiate resourceServer");
			return;
		}

		if (!($resourceServer->validateAccessToken())) {
			throw new Exception(
				"No access token is present in this request.");
		}

		$accessTokenOwnerId = $resourceServer->getChecker()->getOwnerId();
		Log::info("Access token owner id", [$accessTokenOwnerId]);

		if ($entityId !== $accessTokenOwnerId) {
			throw new HttpException(403,
				"Your access token is not valid for this operation");
		}

	}
}
