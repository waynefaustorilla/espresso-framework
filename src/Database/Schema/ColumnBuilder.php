<?php

declare(strict_types=1);

namespace Espresso\Database\Schema;

use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\Table;

class ColumnBuilder {
  public function __construct(
    private readonly Table $table,
    private readonly Column $column,
    private readonly string $name,
  ) {}

  public function nullable(): static {
    $this->column->setNotnull(false);
    return $this;
  }

  public function default(mixed $value): static {
    $this->column->setDefault($value);
    return $this;
  }

  public function unsigned(): static {
    $this->column->setUnsigned(true);
    return $this;
  }

  public function primary(): static {
    $this->table->setPrimaryKey([$this->name]);
    return $this;
  }

  public function unique(?string $indexName = null): static {
    $this->table->addUniqueIndex([$this->name], $indexName);
    return $this;
  }

  public function index(?string $indexName = null): static {
    $this->table->addIndex([$this->name], $indexName);
    return $this;
  }

  public function comment(string $comment): static {
    $this->column->setComment($comment);
    return $this;
  }

  public function constrained(string $referencedTable, string $referencedColumn = "id", string $onDelete = "RESTRICT"): static {
    $this->table->addForeignKeyConstraint(
      $referencedTable,
      [$this->name],
      [$referencedColumn],
      ["onDelete" => $onDelete],
    );
    return $this;
  }
}