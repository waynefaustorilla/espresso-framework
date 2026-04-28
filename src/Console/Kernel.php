<?php

declare(strict_types=1);

namespace Espresso\Console;

use Espresso\Console\Command\CommandRegistryInterface;
use Symfony\Component\Console\Application;

class Kernel {
  public function __construct(private readonly CommandRegistryInterface $commandRegistry) {}

  public function handle(): int {
    $app = new Application("Espresso", "1.0.0");
    $app->setAutoExit(false);
    $app->addCommands($this->commandRegistry->all());

    return $app->run();
  }
}
