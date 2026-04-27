<?php

declare(strict_types=1);

namespace Espresso\Console\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MigrateRollbackCommand extends Command {
  protected function configure(): void {
    $this
      ->setName("migrate:rollback")
      ->setDescription("Roll back a migration — use migrations:execute --down VERSION");
  }

  protected function execute(InputInterface $input, OutputInterface $output): int {
    $output->writeln("<info>Use 'migrations:execute --down VERSION' to roll back a specific migration version.</info>");
    return self::SUCCESS;
  }
}

