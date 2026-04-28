<?php

declare(strict_types=1);

namespace Espresso\Cache;

use Espresso\Cache\Adapter\AdapterFactoryInterface;
use Symfony\Contracts\Cache\CacheInterface;

class CacheFactory {
  public function __construct(private readonly AdapterFactoryInterface $adapterFactory) {}

  public function create(array $config): CacheInterface {
    $driver = $config["default"];
    $driverConfig = $config["drivers"][$driver] ?? $config["drivers"]["file"];
    $ttl = $config["ttl"] ?? 3600;
    $adapter = $driverConfig["adapter"] ?? "filesystem";

    return $this->adapterFactory->create($adapter, $driverConfig, $ttl);
  }
}
