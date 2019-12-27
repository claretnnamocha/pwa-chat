<?php
session_start();

# Autoload files
require_once 'vendor/autoload.php';

# Setups and confugurations
require_once 'config.php';

use MiladRahimi\PhpRouter\Exceptions\RouteNotFoundException;

try {
	require_once 'routes.php';
	set_router($router);
	$router->dispatch();
} catch (RouteNotFoundException $e) {
	die(render('error.index',[ 'code' => 404, 'title' => 'Page not Found', 'message' => 'The page you requested was not found',  'error' => $e ]));
} catch (Throwable $e) {
	die(render('error.index',[ 'code' => 500, 'title' => 'Internal server error', 'message' => $e->getMessage(), 'error' => $e ]));
}