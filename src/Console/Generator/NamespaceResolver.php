<?php

declare(strict_types=1);

namespace Espresso\Console\Generator;

use Espresso\Application;

class NamespaceResolver {
  private array $map = [
    "controller" => ["namespace" => "Espresso\\Http\\Controllers", "path" => "src/Http/Controllers"],
    "service" => ["namespace" => "Espresso\\Services", "path" => "src/Services"],
    "entity" => ["namespace" => "Espresso\\Database\\Entities", "path" => "src/Database/Entities"],
    "repository" => ["namespace" => "Espresso\\Database\\Repository", "path" => "src/Database/Repository"],
    "request" => ["namespace" => "Espresso\\Http\\Requests", "path" => "src/Http/Requests"],
    "mail" => ["namespace" => "Espresso\\Mail", "path" => "src/Mail"],
    "migration" => ["namespace" => "Database\\Migrations", "path" => "database/migrations"],
  ];

  public function getNamespace(string $type): string {
    return $this->map[$type]["namespace"] ?? throw new \RuntimeException("Unknown type [{$type}].");
  }

  public function getAbsolutePath(string $type): string {
    return Application::basePath($this->map[$type]["path"] ?? throw new \RuntimeException("Unknown type [{$type}]."));
  }
}