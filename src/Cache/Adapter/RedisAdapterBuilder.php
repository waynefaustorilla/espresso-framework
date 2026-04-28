<?php

declare(strict_types=1);

namespace Espresso\Cache\Adapter;

use Symfony\Component\Cache\Adapter\RedisAdapter;

class RedisAdapterBuilder {
  public function build(array $config, int $ttl): RedisAdapter {
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