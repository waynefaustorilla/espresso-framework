<?php

declare(strict_types=1);

namespace Espresso\Cache;

use RuntimeException;
use Symfony\Component\Cache\Adapter\ApcuAdapter;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Contracts\Cache\CacheInterface;

class CacheFactory {
  public static function create(array $config): CacheInterface {
    $driver = $config["default"];
    $driverConfig = $config["drivers"][$driver] ?? $config["drivers"]["file"];
    $ttl = $config["ttl"] ?? 3600;

    return match ($driverConfig["adapter"] ?? "filesystem") {
      "filesystem" => new FilesystemAdapter("framework", $ttl, $driverConfig["path"]),
      "redis" => self::createRedisAdapter($driverConfig, $ttl),
      "apcu" => new ApcuAdapter("framework", $ttl),
      default => throw new RuntimeException("Unsupported cache adapter [{$driverConfig['adapter']}]."),
    };
  }

  private static function createRedisAdapter(array $config, int $ttl): RedisAdapter {
    $dsn = sprintf(
      "redis://%s%s:%d",
      $config["password"] ? ":{$config['password']}@" : "",
      $config["host"],
      $config["port"],
    );

    $redis = RedisAdapter::createConnection($dsn);
    return new RedisAdapter($redis, "framework", $ttl);
  }
}
