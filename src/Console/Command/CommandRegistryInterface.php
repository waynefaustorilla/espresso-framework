<?php

declare(strict_types=1);

namespace Espresso\Console\Command;

use Symfony\Component\Console\Command\Command;

interface CommandRegistryInterface {
  public function register(Command $command): void;
  public function all(): array;
}