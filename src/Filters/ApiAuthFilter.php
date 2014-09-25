<?php
/*
 * ApiAuthUserOwnerFilter
 * Checks that oauth token owner ID matches the user ID being displayed/modified.
 */

namespace OriginalMind\ApiAuth\Filters;

use App;
use Log;

class ApiAuthUserOwnerFilter {

	protected $resourceServerName;

	public function __construct($resourceServerName) {
		$this->resourceServerName = $resourceServerName;
	}

	public function filter() {

		Log::info("Running ApiAuthUserOwnerFilter");

		$resourceServer = App::make($this->resourceServerName);

		// We can't compare the user credentials to the oauth token.
		if ($resourceServer === null) {
			Log::error("Unable to instantiate resourceServer");
			return;
		}

		if (!$resourceServer->validateAccessToken()) {
			Log::error("No access_token is present in this request.");
			return;
		}

		if (\Input::get("id") !== $resourceServer->getChecker()->getOwnerId()) {
			return \Response::json(array(
				'error' => 'Your access token is not valid for this operation',
			), 403);
		}

	}
}
