<?php
require '../helpers.php';
require basePath('Database.php');
require basePath('Router.php');

//Instating the router
$router = new Router();

//Get routes
$routes = require basePath('routes.php');

//Get current uri and http method
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

//route the request
$router->route($uri, $method);
