<?php
require __DIR__ . '/../vendor/autoload.php';


use Framework\Router;
use Framework\Session;

Session::start();

require '../helpers.php';

//Instating the router
$router = new Router();

//Get routes
$routes = require basePath('routes.php');

//Get current uri and http method
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);


//route the request
$router->route($uri);
