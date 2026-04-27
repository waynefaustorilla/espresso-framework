<?php

declare(strict_types=1);

return [
  "default" => $_ENV["LOG_CHANNEL"] ?? "file",
  "channels" => [
    "file" => [
      "driver" => "stream",
      "path" => dirname(__DIR__) . "/storage/logs/app.log",
      "level" => $_ENV["LOG_LEVEL"] ?? "debug",
    ],
    "errorlog" => [
      "driver" => "errorlog",
      "level" => $_ENV["LOG_LEVEL"] ?? "error",
    ],
  ],
];
