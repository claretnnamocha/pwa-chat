<?php

use Dotenv\Dotenv;
use MiladRahimi\PhpRouter\Router;
use AlphaORM\AlphaORM as DB;

# Disable Error reporting
ini_set('display_errors','0'); error_reporting(0);

# Allow for CORS 
header("Access-Control-Allow-Origin: *");

# Load environment variables
$dotenv = Dotenv::create(__DIR__);
$dotenv->load();

# Fallback incase .env is not allowed
require_once '.env.php';

# Initializing router class for request handling
$router = new Router(env('BASE_URL'),'Alpha\Controllers');

# Setting up database connection
$driver = env(env('ENV').'_DB_DRIVER');
$host = env(env('ENV').'_DB_HOST');
$database = env(env('ENV').'_DB_NAME');
$user = env(env('ENV').'_DB_USER');
$password = env(env('ENV').'_DB_PASSWORD');
$port = env(env('ENV').'_DB_PORT');

$options = compact('host','driver','user','password','driver','database');
DB::setup($driver, $options);