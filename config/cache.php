<?php

declare(strict_types=1);

return [
  "default" => $_ENV["CACHE_DRIVER"] ?? "file",
  "drivers" => [
    "file" => [
      "adapter" => "filesystem",
      "path" => dirname(__DIR__) . "/storage/cache",
    ],
    "redis" => [
      "adapter" => "redis",
      "host" => $_ENV["REDIS_HOST"] ?? "127.0.0.1",
      "port" => (int) ($_ENV["REDIS_PORT"] ?? 6379),
      "password" => $_ENV["REDIS_PASSWORD"] ?? null,
    ],
    "apcu" => [
      "adapter" => "apcu",
    ],
  ],
  "ttl" => (int) ($_ENV["CACHE_TTL"] ?? 3600),
];
