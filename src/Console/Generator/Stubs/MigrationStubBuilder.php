<?php

declare(strict_types=1);

namespace Espresso\Console\Generator\Stubs;

use Espresso\Console\Generator\StubBuilderInterface;

class MigrationStubBuilder implements StubBuilderInterface {
  public function build(string $name): string {
    return <<<PHP
    <?php

    declare(strict_types=1);

    namespace Database\Migrations;

    use Doctrine\DBAL\Schema\Schema;
    use Doctrine\Migrations\AbstractMigration;
    use Espresso\Database\Schema\Blueprint;
    use Espresso\Database\Schema\SchemaBuilder;

    final class {$name} extends AbstractMigration {
      public function getDescription(): string {
        return "{$name}";
      }

      public function up(Schema \$schema): void {
        SchemaBuilder::create(\$schema, "table_name", function (Blueprint \$table): void {
          \$table->id();
          \$table->string("name");
          \$table->timestamps();
        });
      }

      public function down(Schema \$schema): void {
        SchemaBuilder::dropIfExists(\$schema, "table_name");
      }
    }
    PHP;
  }
}