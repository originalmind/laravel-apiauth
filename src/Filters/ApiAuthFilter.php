<?php
/*
 * ApiAuthFilter
 * Performs common API authorisation tasks
 */

namespace AltTab\Filters;

class ApiAuthFilter {
 
  public function filter()
  {
  	// temporarily reversed to force the error
  	if (Auth::id() === ResourceServer::getOwnerId())
      return Response::json(array(
      'error' => 'Your access token is not valid'
    ), 403);
  }
 
}

