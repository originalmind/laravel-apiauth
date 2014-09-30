laravel-apiauth
===============

A collection of filters to assist with protecting API routes.
Currently only supports the Resource Server implementation from the 
https://packagist.org/packages/lucadegasperi/oauth2-server-laravel package.

** Work in Progress!**
This package needs more work to be made generic.

Installation
--------------

**composer.json**:

```
"require": {
	"originalmind/apiauth": "0.1.*"	
}
```

**app/config/app.php**:

Add the following to your service provider array:

```
'OriginalMind\ApiAuth\ApiAuthServiceProvider',
```

**in your controller constructor**:

```
<?php

class MyController extends \BaseController {

	public function __construct() {
		// OAuth token checking - from an OAuth package
		$this->beforeFilter('oauth:admin', array('only' => array('index', 'destroy')));
		$this->beforeFilter('oauth:enduser', array('only' => array('show', 'update')));

		// Token owner checking - from this package
		$this->beforeFilter('apiauthuserowner:mymodel', array('only' => array('show', 'update')));
	}

?>
```