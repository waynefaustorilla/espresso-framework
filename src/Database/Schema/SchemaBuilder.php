<?php

declare(strict_types=1);

namespace Espresso\Database\Schema;

use Doctrine\DBAL\Schema\Schema;

class SchemaBuilder {
  public static function create(Schema $schema, string $table, callable $callback): void {
    $blueprint = new Blueprint($schema->createTable($table));
    $callback($blueprint);
  }

  public static function table(Schema $schema, string $table, callable $callback): void {
    $blueprint = new Blueprint($schema->getTable($table));
    $callback($blueprint);
  }

  public static function drop(Schema $schema, string $table): void {
    $schema->dropTable($table);
  }

  public static function dropIfExists(Schema $schema, string $table): void {
    if ($schema->hasTable($table)) {
      $schema->dropTable($table);
    }
  }

  public static function rename(Schema $schema, string $from, string $to): void {
    $schema->renameTable($from, $to);
  }

  public static function hasTable(Schema $schema, string $table): bool {
    return $schema->hasTable($table);
  }
}