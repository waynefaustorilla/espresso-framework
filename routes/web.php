<?php

declare(strict_types=1);

use Espresso\Http\Controllers\TodoController;
use Espresso\Http\Controllers\WelcomeController;
use League\Route\Router;
use Psr\Container\ContainerInterface;

/** @var Router $router */
/** @var ContainerInterface $container */

$router->map("GET", "/", [WelcomeController::class, "index"]);

$router->map("GET",  "/todos",                    [TodoController::class, "index"]);
$router->map("GET",  "/todos/create",             [TodoController::class, "create"]);
$router->map("POST", "/todos",                    [TodoController::class, "store"]);
$router->map("GET",  "/todos/{id}/edit",          [TodoController::class, "edit"]);
$router->map("POST", "/todos/{id}/update",        [TodoController::class, "update"]);
$router->map("POST", "/todos/{id}/toggle",        [TodoController::class, "toggle"]);
$router->map("POST", "/todos/{id}/delete",        [TodoController::class, "destroy"]);
