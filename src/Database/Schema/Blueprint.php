<?php

declare(strict_types=1);

namespace Espresso\Database\Schema;

use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Types;

class Blueprint {
  public function __construct(private readonly Table $table) {}

  public function id(): ColumnBuilder {
    $column = $this->table->addColumn("id", Types::INTEGER, ["autoincrement" => true]);
    $this->table->setPrimaryKey(["id"]);
    return new ColumnBuilder($this->table, $column, "id");
  }

  public function bigId(string $name = "id"): ColumnBuilder {
    $column = $this->table->addColumn($name, Types::BIGINT, ["autoincrement" => true]);
    $this->table->setPrimaryKey([$name]);
    return new ColumnBuilder($this->table, $column, $name);
  }

  public function uuid(string $name = "id"): ColumnBuilder {
    $column = $this->table->addColumn($name, Types::GUID);
    return new ColumnBuilder($this->table, $column, $name);
  }

  public function string(string $name, int $length = 255): ColumnBuilder {
    $column = $this->table->addColumn($name, Types::STRING, ["length" => $length]);
    return new ColumnBuilder($this->table, $column, $name);
  }

  public function char(string $name, int $length = 255): ColumnBuilder {
    $column = $this->table->addColumn($name, Types::STRING, ["length" => $length, "fixed" => true]);
    return new ColumnBuilder($this->table, $column, $name);
  }

  public function text(string $name): ColumnBuilder {
    $column = $this->table->addColumn($name, Types::TEXT);
    return new ColumnBuilder($this->table, $column, $name);
  }

  public function integer(string $name): ColumnBuilder {
    $column = $this->table->addColumn($name, Types::INTEGER);
    return new ColumnBuilder($this->table, $column, $name);
  }

  public function smallInteger(string $name): ColumnBuilder {
    $column = $this->table->addColumn($name, Types::SMALLINT);
    return new ColumnBuilder($this->table, $column, $name);
  }

  public function bigInteger(string $name): ColumnBuilder {
    $column = $this->table->addColumn($name, Types::BIGINT);
    return new ColumnBuilder($this->table, $column, $name);
  }

  public function boolean(string $name): ColumnBuilder {
    $column = $this->table->addColumn($name, Types::BOOLEAN);
    return new ColumnBuilder($this->table, $column, $name);
  }

  public function decimal(string $name, int $precision = 8, int $scale = 2): ColumnBuilder {
    $column = $this->table->addColumn($name, Types::DECIMAL, ["precision" => $precision, "scale" => $scale]);
    return new ColumnBuilder($this->table, $column, $name);
  }

  public function float(string $name): ColumnBuilder {
    $column = $this->table->addColumn($name, Types::FLOAT);
    return new ColumnBuilder($this->table, $column, $name);
  }

  public function date(string $name): ColumnBuilder {
    $column = $this->table->addColumn($name, Types::DATE_IMMUTABLE);
    return new ColumnBuilder($this->table, $column, $name);
  }

  public function dateTime(string $name): ColumnBuilder {
    $column = $this->table->addColumn($name, Types::DATETIME_IMMUTABLE);
    return new ColumnBuilder($this->table, $column, $name);
  }

  public function time(string $name): ColumnBuilder {
    $column = $this->table->addColumn($name, Types::TIME_IMMUTABLE);
    return new ColumnBuilder($this->table, $column, $name);
  }

  public function json(string $name): ColumnBuilder {
    $column = $this->table->addColumn($name, Types::JSON);
    return new ColumnBuilder($this->table, $column, $name);
  }

  public function blob(string $name): ColumnBuilder {
    $column = $this->table->addColumn($name, Types::BLOB);
    return new ColumnBuilder($this->table, $column, $name);
  }

  public function foreignId(string $name): ColumnBuilder {
    $column = $this->table->addColumn($name, Types::BIGINT);
    return new ColumnBuilder($this->table, $column, $name);
  }

  public function timestamps(): void {
    $this->table->addColumn("created_at", Types::DATETIME_IMMUTABLE);
    $this->table->addColumn("updated_at", Types::DATETIME_IMMUTABLE, ["notnull" => false]);
  }

  public function softDeletes(): ColumnBuilder {
    $column = $this->table->addColumn("deleted_at", Types::DATETIME_IMMUTABLE, ["notnull" => false]);
    return new ColumnBuilder($this->table, $column, "deleted_at");
  }

  public function primary(array $columns, ?string $indexName = null): void {
    $this->table->setPrimaryKey($columns, $indexName);
  }

  public function unique(array $columns, ?string $indexName = null): void {
    $this->table->addUniqueIndex($columns, $indexName);
  }

  public function index(array $columns, ?string $indexName = null): void {
    $this->table->addIndex($columns, $indexName);
  }

  public function dropColumn(string $name): void {
    $this->table->dropColumn($name);
  }
}