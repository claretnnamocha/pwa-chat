<?php
use MiladRahimi\PhpRouter\Router;
use Alpha\Middlewares\Auth;

$router->group([
	'prefix' => '/',
	'middleware' => []
],function (Router $router) {
	$router->name('home')->any('','Controller@index');
	$router->name('chat')->any('chat','Controller@chat');
	$router->name('setup')->any('setup/?','Controller@setup');
	$router->name('signout')->any('signout/?','Controller@signout');
	$router->name('verify')->any('verify/?','Controller@verify');
	$router->name('404')->any('404/?','Controller@_404');
	$router->name('assets')->any('get_assets/?','Controller@assets');
	
	$router->name('graphql')->post('graphql/?','Controller@graphql');
	$router->name('update-profile')->post('update-profile/?','Controller@update_profile');
});