<?php
$phpRouter = null;

function set_router($router)
{
	global $phpRouter;
	$phpRouter = $router;
}

function get_router()
{
	global $phpRouter;
	return $phpRouter;
}

function url($name, $options=[])
{
	global $phpRouter;
	return $phpRouter->url($name, $options);
}

function current_url()
{
	return $_SERVER['REQUEST_URI'];
}

function redirect($url)
{
	header("location: $url");
}