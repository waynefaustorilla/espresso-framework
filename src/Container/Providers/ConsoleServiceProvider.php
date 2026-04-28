<?php

declare(strict_types=1);

namespace Espresso\Container\Providers;

use DI\Container;
use DI\ContainerBuilder;
use Doctrine\Migrations\Configuration\EntityManager\ExistingEntityManager;
use Doctrine\Migrations\Configuration\Migration\PhpFile;
use Doctrine\Migrations\DependencyFactory;
use Doctrine\Migrations\Tools\Console\Command\DiffCommand;
use Doctrine\Migrations\Tools\Console\Command\ExecuteCommand;
use Doctrine\Migrations\Tools\Console\Command\GenerateCommand;
use Doctrine\Migrations\Tools\Console\Command\MigrateCommand as DoctrineMigrateCommand;
use Doctrine\Migrations\Tools\Console\Command\StatusCommand;
use Doctrine\ORM\EntityManager;
use Espresso\Console\Command\CommandRegistry;
use Espresso\Console\Command\CommandRegistryInterface;
use Espresso\Console\Commands\MakeControllerCommand;
use Espresso\Console\Commands\MakeEntityCommand;
use Espresso\Console\Commands\MakeMailCommand;
use Espresso\Console\Commands\MakeMigrationCommand;
use Espresso\Console\Commands\MakeRepositoryCommand;
use Espresso\Console\Commands\MakeRequestCommand;
use Espresso\Console\Commands\MakeServiceCommand;
use Espresso\Console\Commands\MakeViewCommand;
use Espresso\Console\Commands\MigrateRollbackCommand;
use Espresso\Console\Commands\ServeCommand;
use Espresso\Console\Generator\FileWriter;
use Espresso\Console\Generator\NamespaceResolver;
use Espresso\Console\Generator\Stubs\ControllerStubBuilder;
use Espresso\Console\Generator\Stubs\EntityStubBuilder;
use Espresso\Console\Generator\Stubs\MailStubBuilder;
use Espresso\Console\Generator\Stubs\MigrationStubBuilder;
use Espresso\Console\Generator\Stubs\RepositoryStubBuilder;
use Espresso\Console\Generator\Stubs\RequestStubBuilder;
use Espresso\Console\Generator\Stubs\ServiceStubBuilder;
use Espresso\Console\Kernel as ConsoleKernel;
use Espresso\Container\ServiceProviderInterface;

class ConsoleServiceProvider implements ServiceProviderInterface {
  public function register(ContainerBuilder $builder): void {
    $builder->addDefinitions([
      FileWriter::class => fn() => new FileWriter(),
      NamespaceResolver::class => fn() => new NamespaceResolver(),

      DependencyFactory::class => function (Container $c): DependencyFactory {
        $migrationsConfig = new PhpFile($c->get("basePath") . "/config/migrations.php");
        return DependencyFactory::fromEntityManager(
          $migrationsConfig,
          new ExistingEntityManager($c->get(EntityManager::class)),
        );
      },

      CommandRegistryInterface::class => function (Container $c): CommandRegistry {
        $registry = new CommandRegistry();
        $fileWriter = $c->get(FileWriter::class);
        $namespaceResolver = $c->get(NamespaceResolver::class);

        $registry->register(new ServeCommand());
        $registry->register(new MigrateRollbackCommand());
        $registry->register(new DoctrineMigrateCommand($c->get(DependencyFactory::class)));
        $registry->register(new DiffCommand($c->get(DependencyFactory::class)));
        $registry->register(new StatusCommand($c->get(DependencyFactory::class)));
        $registry->register(new GenerateCommand($c->get(DependencyFactory::class)));
        $registry->register(new ExecuteCommand($c->get(DependencyFactory::class)));
        $registry->register(new MakeControllerCommand(new ControllerStubBuilder(), $fileWriter, $namespaceResolver));
        $registry->register(new MakeEntityCommand(new EntityStubBuilder(), $fileWriter, $namespaceResolver));
        $registry->register(new MakeMailCommand(new MailStubBuilder(), $fileWriter, $namespaceResolver));
        $registry->register(new MakeMigrationCommand(new MigrationStubBuilder(), $fileWriter, $namespaceResolver));
        $registry->register(new MakeRepositoryCommand(new RepositoryStubBuilder(), $fileWriter, $namespaceResolver));
        $registry->register(new MakeRequestCommand(new RequestStubBuilder(), $fileWriter, $namespaceResolver));
        $registry->register(new MakeServiceCommand(new ServiceStubBuilder(), $fileWriter, $namespaceResolver));
        $registry->register(new MakeViewCommand($fileWriter));

        return $registry;
      },

      ConsoleKernel::class => function (Container $c): ConsoleKernel {
        return new ConsoleKernel($c->get(CommandRegistryInterface::class));
      },
    ]);
  }
}