<?php

declare(strict_types=1);

use Doctrine\Migrations\Configuration\EntityManager\ExistingEntityManager;
use Doctrine\Migrations\Configuration\Migration\PhpFile;
use Doctrine\Migrations\DependencyFactory;
use Doctrine\ORM\EntityManager;
use Espresso\Application;

require __DIR__ . "/../vendor/autoload.php";

$app = new Application(__DIR__ . "/..");
$container = $app->getContainer();

$config = new PhpFile(__DIR__ . "/../config/migrations.php");

return DependencyFactory::fromEntityManager(
  $config,
  new ExistingEntityManager($container->get(EntityManager::class)),
);
