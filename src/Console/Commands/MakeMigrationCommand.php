<?php

declare(strict_types=1);

namespace Espresso\Console\Commands;

use Espresso\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MakeMigrationCommand extends Command
{
  protected function configure(): void
  {
    $this
      ->setName("make:migration")
      ->setDescription("Create a new blank Doctrine migration file")
      ->addArgument("name", InputArgument::REQUIRED, "Descriptive name in PascalCase (e.g. CreateUsersTable)");
  }

  protected function execute(InputInterface $input, OutputInterface $output): int
  {
    $name = (string) $input->getArgument("name");
    $className = $this->toPascalCase($name);
    $timestamp = date("YmdHis");
    $fileName = "{$timestamp}_{$className}";
    $targetDir = Application::basePath("database/migrations");

    if (!is_dir($targetDir)) {
      mkdir($targetDir, 0755, true);
    }

    $targetFile = $targetDir . "/{$fileName}.php";
    file_put_contents($targetFile, $this->buildStub($className, $name));

    $output->writeln("<info>Migration created: database/migrations/{$fileName}.php</info>");
    return Command::SUCCESS;
  }

  private function toPascalCase(string $name): string
  {
    $name = preg_replace('/[^a-zA-Z0-9]+/', ' ', $name);
    return str_replace(' ', '', ucwords(strtolower((string) $name)));
  }

  private function buildStub(string $className, string $description): string
  {
    return <<<PHP
<?php

declare(strict_types=1);

namespace Database\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Espresso\Database\Schema\Blueprint;
use Espresso\Database\Schema\SchemaBuilder;

final class {$className} extends AbstractMigration {
  public function getDescription(): string {
    return "{$description}";
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
