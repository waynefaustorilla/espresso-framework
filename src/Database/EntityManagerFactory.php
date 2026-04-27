<?php

declare(strict_types=1);

namespace Espresso\Database;

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\AttributeDriver;
use Doctrine\ORM\ORMSetup;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class EntityManagerFactory {
  public static function create(array $config): EntityManager {
    $connection = $config["default"];
    $connectionParams = $config["connections"][$connection];
    $entityPaths = $config["entity_paths"];
    $isDevMode = (bool) ($_ENV["APP_DEBUG"] ?? false);

    $ormConfig = ORMSetup::createAttributeMetadataConfiguration(
      $entityPaths,
      $isDevMode,
      null,
      $isDevMode ? new ArrayAdapter() : new FilesystemAdapter("doctrine_metadata", 0, $config["migrations_path"] . "/../cache"),
    );

    $dbalConnection = DriverManager::getConnection($connectionParams, $ormConfig);

    return new EntityManager($dbalConnection, $ormConfig);
  }
}
