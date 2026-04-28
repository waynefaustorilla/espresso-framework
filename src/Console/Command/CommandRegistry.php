<?php

declare(strict_types=1);

namespace Espresso\Console\Command;

use Symfony\Component\Console\Command\Command;

class CommandRegistry implements CommandRegistryInterface {
  private array $commands = [];

  public function register(Command $command): void {
    $this->commands[] = $command;
  }

  public function all(): array {
    return $this->commands;
  }
}