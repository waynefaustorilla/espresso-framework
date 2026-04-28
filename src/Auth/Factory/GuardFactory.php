<?php

declare(strict_types=1);

namespace Espresso\Auth\Factory;

use Espresso\Auth\Contracts\GuardInterface;
use RuntimeException;

class GuardFactory implements GuardFactoryInterface {
  private array $creators = [];

  public function register(string $driver, callable $creator): void {
    $this->creators[$driver] = $creator;
  }

  public function create(string $driver, array $config): GuardInterface {
    if (!isset($this->creators[$driver])) {
      throw new RuntimeException("Unsupported auth driver [{$driver}].");
    }

    return ($this->creators[$driver])($config);
  }
}