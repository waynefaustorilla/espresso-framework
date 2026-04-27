<?php

declare(strict_types=1);

namespace Espresso\Console;

use Doctrine\Migrations\Tools\Console\Command\DiffCommand;
use Doctrine\Migrations\Tools\Console\Command\ExecuteCommand;
use Doctrine\Migrations\Tools\Console\Command\GenerateCommand;
use Doctrine\Migrations\Tools\Console\Command\MigrateCommand;
use Doctrine\Migrations\Tools\Console\Command\StatusCommand;
use Espresso\Console\Commands\MakeControllerCommand;
use Espresso\Console\Commands\MakeEntityCommand;
use Espresso\Console\Commands\MakeMigrationCommand;
use Espresso\Console\Commands\MakeRepositoryCommand;
use Espresso\Console\Commands\MakeRequestCommand;
use Espresso\Console\Commands\MakeServiceCommand;
use Espresso\Console\Commands\MakeViewCommand;
use Espresso\Console\Commands\MigrateRollbackCommand;
use Espresso\Console\Commands\ServeCommand;
use Symfony\Component\Console\Application;

class Kernel {
  public function __construct(
    private readonly MakeControllerCommand $makeControllerCommand,
    private readonly MakeEntityCommand $makeEntityCommand,
    private readonly MakeMigrationCommand $makeMigrationCommand,
    private readonly MakeRepositoryCommand $makeRepositoryCommand,
    private readonly MakeRequestCommand $makeRequestCommand,
    private readonly MakeServiceCommand $makeServiceCommand,
    private readonly MakeViewCommand $makeViewCommand,
    private readonly MigrateRollbackCommand $migrateRollbackCommand,
    private readonly ServeCommand $serveCommand,
    private readonly MigrateCommand $migrateCommand,
    private readonly DiffCommand $diffCommand,
    private readonly StatusCommand $statusCommand,
    private readonly GenerateCommand $generateCommand,
    private readonly ExecuteCommand $executeCommand,
  ) {}

  public function handle(): int {
    $app = new Application("Espresso", "1.0.0");
    $app->setAutoExit(false);

    $app->addCommands([
      $this->serveCommand,
      $this->migrateCommand,
      $this->migrateRollbackCommand,
      $this->diffCommand,
      $this->statusCommand,
      $this->generateCommand,
      $this->executeCommand,
      $this->makeControllerCommand,
      $this->makeEntityCommand,
      $this->makeMigrationCommand,
      $this->makeRepositoryCommand,
      $this->makeRequestCommand,
      $this->makeServiceCommand,
      $this->makeViewCommand,
    ]);

    return $app->run();
  }
}
