<?php

declare(strict_types=1);

return [
  "name" => $_ENV["APP_NAME"] ?? "Espresso",
  "env" => $_ENV["APP_ENV"] ?? "production",
  "debug" => filter_var($_ENV["APP_DEBUG"] ?? false, FILTER_VALIDATE_BOOLEAN),
  "url" => $_ENV["APP_URL"] ?? "http://localhost",
  "key" => $_ENV["APP_KEY"] ?? "",
  "views" => dirname(__DIR__) . "/resources/views",
  "storage" => dirname(__DIR__) . "/storage",
];
