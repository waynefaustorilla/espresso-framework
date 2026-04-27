<?php

declare(strict_types=1);

namespace Database\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Espresso\Database\Schema\Blueprint;
use Espresso\Database\Schema\SchemaBuilder;

final class CreateUsersTable extends AbstractMigration {
  public function getDescription(): string {
    return "Create users table";
  }

  public function up(Schema $schema): void {
    SchemaBuilder::create($schema, "users", function (Blueprint $table): void {
      $table->id();
      $table->string("name");
      $table->string("email")->unique();
      $table->string("password");
      $table->dateTime("created_at");
    });
  }

  public function down(Schema $schema): void {
    SchemaBuilder::dropIfExists($schema, "users");
  }
}