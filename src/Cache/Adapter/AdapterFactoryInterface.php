<?php

declare(strict_types=1);

namespace Espresso\Cache\Adapter;

use Symfony\Contracts\Cache\CacheInterface;

interface AdapterFactoryInterface {
  public function create(string $adapter, array $config, int $ttl): CacheInterface;
}