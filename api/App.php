<?php
require "vendor/autoload.php";

use libraries\Database;
use libraries\Router;

$database = new Database();
$router = new Router($database->getPDO());
$router->initializeRoutes();
