<?php

declare(strict_types=1);

namespace Espresso\Container\Providers;

use DI\Container;
use DI\ContainerBuilder;
use Doctrine\ORM\EntityManager;
use Espresso\Container\ServiceProviderInterface;
use Espresso\Database\EntityManagerFactory;

class DatabaseServiceProvider implements ServiceProviderInterface {
  public function register(ContainerBuilder $builder): void {
    $builder->addDefinitions([
      EntityManager::class => function (Container $c): EntityManager {
        return EntityManagerFactory::create($c->get("config")["database"]);
      },
    ]);
  }
}