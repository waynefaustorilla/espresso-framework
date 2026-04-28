<?php

declare(strict_types=1);

namespace Espresso\Logging\Handler;

use Monolog\Handler\HandlerInterface;
use RuntimeException;

class HandlerRegistry implements HandlerFactoryInterface {
  private array $creators = [];

  public function register(string $driver, callable $creator): void {
    $this->creators[$driver] = $creator;
  }

  public function create(string $driver, array $config, int $level): HandlerInterface {
    if (!isset($this->creators[$driver])) {
      throw new RuntimeException("Unsupported log handler [{$driver}].");
    }

    return ($this->creators[$driver])($config, $level);
  }
}