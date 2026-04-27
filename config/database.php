<?php

declare(strict_types=1);

return [
  "default" => $_ENV["DB_CONNECTION"] ?? "mysql",
  "connections" => [
    "mysql" => [
      "driver" => "pdo_mysql",
      "host" => $_ENV["DB_HOST"] ?? "127.0.0.1",
      "port" => (int) ($_ENV["DB_PORT"] ?? 3306),
      "dbname" => $_ENV["DB_DATABASE"] ?? "framework",
      "user" => $_ENV["DB_USERNAME"] ?? "root",
      "password" => $_ENV["DB_PASSWORD"] ?? "",
      "charset" => "utf8mb4",
    ],
    "sqlite" => [
      "driver" => "pdo_sqlite",
      "path" => $_ENV["DB_DATABASE"] ?? dirname(__DIR__) . "/database/database.sqlite",
    ],
    "pgsql" => [
      "driver" => "pdo_pgsql",
      "host" => $_ENV["DB_HOST"] ?? "127.0.0.1",
      "port" => (int) ($_ENV["DB_PORT"] ?? 5432),
      "dbname" => $_ENV["DB_DATABASE"] ?? "framework",
      "user" => $_ENV["DB_USERNAME"] ?? "postgres",
      "password" => $_ENV["DB_PASSWORD"] ?? "",
      "charset" => "utf8",
    ],
  ],
  "migrations_path" => dirname(__DIR__) . "/database/migrations",
  "entity_paths" => [
    dirname(__DIR__) . "/src/Database/Entities",
  ],
];
