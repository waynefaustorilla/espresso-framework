<?php

declare(strict_types=1);

namespace Espresso\Cache\Adapter;

use RuntimeException;
use Symfony\Contracts\Cache\CacheInterface;

class AdapterRegistry implements AdapterFactoryInterface {
  private array $creators = [];

  public function register(string $adapter, callable $creator): void {
    $this->creators[$adapter] = $creator;
  }

  public function create(string $adapter, array $config, int $ttl): CacheInterface {
    if (!isset($this->creators[$adapter])) {
      throw new RuntimeException("Unsupported cache adapter [{$adapter}].");
    }

    return ($this->creators[$adapter])($config, $ttl);
  }
}