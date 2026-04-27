<?php

declare(strict_types=1);

namespace Database\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Espresso\Database\Schema\Blueprint;
use Espresso\Database\Schema\SchemaBuilder;

final class CreateTodosTable extends AbstractMigration {
  public function getDescription(): string {
    return "Create todos table";
  }

  public function up(Schema $schema): void {
    SchemaBuilder::create($schema, "todos", function (Blueprint $table): void {
      $table->id();
      $table->foreignId("user_id")->constrained("users", "id", "CASCADE");
      $table->string("title");
      $table->boolean("completed")->default(false);
      $table->dateTime("created_at");
    });
  }

  public function down(Schema $schema): void {
    SchemaBuilder::dropIfExists($schema, "todos");
  }
}