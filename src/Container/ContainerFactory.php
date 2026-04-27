<?php

declare(strict_types=1);

namespace Espresso\Container;

use Doctrine\Migrations\Configuration\EntityManager\ExistingEntityManager;
use Doctrine\Migrations\Configuration\Migration\PhpFile;
use Doctrine\Migrations\DependencyFactory;
use Doctrine\Migrations\Tools\Console\Command\DiffCommand;
use Doctrine\Migrations\Tools\Console\Command\ExecuteCommand;
use Doctrine\Migrations\Tools\Console\Command\GenerateCommand;
use Doctrine\Migrations\Tools\Console\Command\MigrateCommand as DoctrineMigrateCommand;
use Doctrine\Migrations\Tools\Console\Command\RollupCommand;
use Doctrine\Migrations\Tools\Console\Command\StatusCommand;
use DI\Container;
use DI\ContainerBuilder;
use Doctrine\ORM\EntityManager;
use Espresso\Application;
use Espresso\Auth\AuthManager;
use Espresso\Cache\CacheFactory;
use Espresso\Database\EntityManagerFactory;
use Espresso\Database\Repository\TodoRepository;
use Espresso\Http\Controllers\TodoController;
use Espresso\Services\TodoService;
use Espresso\Console\Commands\MakeControllerCommand;
use Espresso\Console\Commands\MakeEntityCommand;
use Espresso\Console\Commands\MakeMigrationCommand;
use Espresso\Console\Commands\MakeRepositoryCommand;
use Espresso\Console\Commands\MakeRequestCommand;
use Espresso\Console\Commands\MakeServiceCommand;
use Espresso\Console\Commands\MakeViewCommand;
use Espresso\Console\Commands\MigrateRollbackCommand;
use Espresso\Console\Commands\ServeCommand;
use Espresso\Console\Kernel as ConsoleKernel;
use Espresso\Http\Kernel as HttpKernel;
use Espresso\Http\Middleware\ExceptionMiddleware;
use Espresso\Http\Router;
use Espresso\Logging\LoggerFactory;
use Espresso\Validation\Validator;
use Espresso\View\LatteFactory;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Latte\Engine;

class ContainerFactory {
  public static function build(array $config, string $basePath): Container {
    $builder = new ContainerBuilder();
    $builder->addDefinitions(self::definitions($config, $basePath));

    return $builder->build();
  }

  private static function definitions(array $config, string $basePath): array {
    return [
      "config" => $config,
      "basePath" => $basePath,

      LoggerInterface::class => function (Container $container): Logger {
        return LoggerFactory::create($container->get("config")["logging"]);
      },

      EntityManager::class => function (Container $container): EntityManager {
        return EntityManagerFactory::create($container->get("config")["database"]);
      },

      AuthManager::class => function (Container $container): AuthManager {
        return new AuthManager(
          $container->get("config")["auth"],
          $container->get(EntityManager::class),
        );
      },

      Engine::class => function (Container $container): Engine {
        return LatteFactory::create(
          $container->get("config")["app"],
          $container->get(AuthManager::class),
        );
      },

      CacheInterface::class => function (Container $container): \Symfony\Contracts\Cache\CacheInterface {
        return CacheFactory::create($container->get("config")["cache"]);
      },

      Validator::class => fn() => new Validator(),

      Router::class => function (Container $container): Router {
        return new Router($container);
      },

      ExceptionMiddleware::class => function (Container $container): ExceptionMiddleware {
        return new ExceptionMiddleware(
          $container->get(Engine::class),
          $container->get(LoggerInterface::class),
          $container->get("config")["app"]["debug"],
        );
      },

      HttpKernel::class => function (Container $container): HttpKernel {
        return new HttpKernel(
          $container->get(Router::class),
          $container->get(ExceptionMiddleware::class),
          $container->get("basePath"),
          $container->get(AuthManager::class),
        );
      },

      DependencyFactory::class => function (Container $container): DependencyFactory {
        $migrationsConfig = new PhpFile($container->get("basePath") . "/config/migrations.php");
        return DependencyFactory::fromEntityManager(
          $migrationsConfig,
          new ExistingEntityManager($container->get(EntityManager::class)),
        );
      },

      DoctrineMigrateCommand::class => function (Container $container): DoctrineMigrateCommand {
        return new DoctrineMigrateCommand($container->get(DependencyFactory::class));
      },

      DiffCommand::class => function (Container $container): DiffCommand {
        return new DiffCommand($container->get(DependencyFactory::class));
      },

      StatusCommand::class => function (Container $container): StatusCommand {
        return new StatusCommand($container->get(DependencyFactory::class));
      },

      GenerateCommand::class => function (Container $container): GenerateCommand {
        return new GenerateCommand($container->get(DependencyFactory::class));
      },

      ExecuteCommand::class => function (Container $container): ExecuteCommand {
        return new ExecuteCommand($container->get(DependencyFactory::class));
      },

      MigrateRollbackCommand::class => fn() => new MigrateRollbackCommand(),

      TodoRepository::class => function (Container $container): TodoRepository {
        return new TodoRepository($container->get(EntityManager::class));
      },

      TodoService::class => function (Container $container): TodoService {
        return new TodoService(
          $container->get(EntityManager::class),
          $container->get(TodoRepository::class),
        );
      },

      TodoController::class => function (Container $container): TodoController {
        return new TodoController(
          $container->get(Engine::class),
          $container->get(Validator::class),
          $container->get(TodoService::class),
        );
      },

      MakeControllerCommand::class => fn() => new MakeControllerCommand(),
      MakeEntityCommand::class => fn() => new MakeEntityCommand(),
      MakeMigrationCommand::class => fn() => new MakeMigrationCommand(),
      MakeRepositoryCommand::class => fn() => new MakeRepositoryCommand(),
      MakeRequestCommand::class => fn() => new MakeRequestCommand(),
      MakeServiceCommand::class => fn() => new MakeServiceCommand(),
      MakeViewCommand::class => fn() => new MakeViewCommand(),
      ServeCommand::class => fn() => new ServeCommand(),

      ConsoleKernel::class => function (Container $container): ConsoleKernel {
        return new ConsoleKernel(
          $container->get(MakeControllerCommand::class),
          $container->get(MakeEntityCommand::class),
          $container->get(MakeMigrationCommand::class),
          $container->get(MakeRepositoryCommand::class),
          $container->get(MakeRequestCommand::class),
          $container->get(MakeServiceCommand::class),
          $container->get(MakeViewCommand::class),
          $container->get(MigrateRollbackCommand::class),
          $container->get(ServeCommand::class),
          $container->get(DoctrineMigrateCommand::class),
          $container->get(DiffCommand::class),
          $container->get(StatusCommand::class),
          $container->get(GenerateCommand::class),
          $container->get(ExecuteCommand::class),
        );
      },
    ];
  }
}
