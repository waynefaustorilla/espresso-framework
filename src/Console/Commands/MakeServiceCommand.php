<?php

declare(strict_types=1);

namespace Espresso\Console\Commands;

use Espresso\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MakeServiceCommand extends Command {
  protected function configure(): void {
    $this
      ->setName("make:service")
      ->setDescription("Create a new service class")
      ->addArgument("name", InputArgument::REQUIRED, "The service class name (e.g. UserService)");
  }

  protected function execute(InputInterface $input, OutputInterface $output): int {
    $name = ucfirst((string) $input->getArgument("name"));

    if (!str_ends_with($name, "Service")) {
      $name .= "Service";
    }

    $targetDir = Application::basePath("src/Services");

    if (!is_dir($targetDir)) {
      mkdir($targetDir, 0755, true);
    }

    $targetFile = $targetDir . "/{$name}.php";

    if (file_exists($targetFile)) {
      $output->writeln("<error>Service {$name} already exists.</error>");
      return Command::FAILURE;
    }

    file_put_contents($targetFile, $this->buildStub($name));

    $output->writeln("<info>Service created: src/Services/{$name}.php</info>");
    return Command::SUCCESS;
  }

  private function buildStub(string $name): string {
    $repositoryName = str_replace("Service", "Repository", $name);

    return <<<PHP
    <?php

    declare(strict_types=1);

    namespace Espresso\Services;

    use Doctrine\ORM\EntityManager;
    use Espresso\Database\Repository\\{$repositoryName};

    class {$name} extends AbstractService {
      public function __construct(
        EntityManager \$entityManager,
        private readonly {$repositoryName} \$repository,
      ) {
        parent::__construct(\$entityManager);
      }
    }
    PHP;
  }
}
