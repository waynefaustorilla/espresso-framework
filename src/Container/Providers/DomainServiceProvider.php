<?php

declare(strict_types=1);

namespace Espresso\Container\Providers;

use DI\Container;
use DI\ContainerBuilder;
use Doctrine\ORM\EntityManager;
use Espresso\Container\ServiceProviderInterface;
use Espresso\Database\Repository\TodoRepository;
use Espresso\Database\Repository\TodoRepositoryInterface;
use Espresso\Services\TodoService;
use Espresso\Services\TodoTransformer;

class DomainServiceProvider implements ServiceProviderInterface {
  public function register(ContainerBuilder $builder): void {
    $builder->addDefinitions([
      TodoRepository::class => function (Container $c): TodoRepository {
        return new TodoRepository($c->get(EntityManager::class));
      },

      TodoRepositoryInterface::class => fn(Container $c) => $c->get(TodoRepository::class),

      TodoTransformer::class => fn() => new TodoTransformer(),

      TodoService::class => function (Container $c): TodoService {
        return new TodoService(
          $c->get(TodoRepositoryInterface::class),
          $c->get(TodoTransformer::class),
        );
      },
    ]);
  }
}