<?php

declare(strict_types=1);

namespace Espresso\Console\Commands;

use Espresso\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MakeRepositoryCommand extends Command {
  protected function configure(): void {
    $this
      ->setName("make:repository")
      ->setDescription("Create a new repository class")
      ->addArgument("name", InputArgument::REQUIRED, "The repository class name or entity name (e.g. User or UserRepository)");
  }

  protected function execute(InputInterface $input, OutputInterface $output): int {
    $name = ucfirst((string) $input->getArgument("name"));

    if (!str_ends_with($name, "Repository")) {
      $entityName = $name;
      $name = $name . "Repository";
    } else {
      $entityName = str_replace("Repository", "", $name);
    }

    $targetDir = Application::basePath("src/Database/Repository");

    $targetFile = $targetDir . "/{$name}.php";

    if (file_exists($targetFile)) {
      $output->writeln("<error>Repository {$name} already exists.</error>");
      return Command::FAILURE;
    }

    file_put_contents($targetFile, $this->buildStub($name, $entityName));

    $output->writeln("<info>Repository created: src/Database/Repository/{$name}.php</info>");
    return Command::SUCCESS;
  }

  private function buildStub(string $name, string $entityName): string {
    return <<<PHP
    <?php

    declare(strict_types=1);

    namespace Espresso\Database\Repository;

    use Espresso\Database\Entities\\{$entityName};

    class {$name} extends AbstractRepository {
      protected function getEntityClass(): string {
        return {$entityName}::class;
      }
    }
    PHP;
  }
}
