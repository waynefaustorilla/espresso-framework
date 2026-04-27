<?php

declare(strict_types=1);

require dirname(__DIR__) . "/vendor/autoload.php";

use Espresso\Application;

$app = new Application(dirname(__DIR__));
$app->handleRequest();
