<?php

declare(strict_types=1);

namespace Espresso\Console\Commands;

use Espresso\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MakeControllerCommand extends Command
{
  protected function configure(): void
  {
    $this
      ->setName("make:controller")
      ->setDescription("Create a new controller class")
      ->addArgument("name", InputArgument::REQUIRED, "The controller class name (e.g. UserController)");
  }

  protected function execute(InputInterface $input, OutputInterface $output): int
  {
    $name = (string) $input->getArgument("name");
    $name = ucfirst($name);

    if (!str_ends_with($name, "Controller")) {
      $name .= "Controller";
    }

    $targetDir = Application::basePath("src/Http/Controllers");

    if (!is_dir($targetDir)) {
      mkdir($targetDir, 0755, true);
    }

    $targetFile = $targetDir . "/{$name}.php";

    if (file_exists($targetFile)) {
      $output->writeln("<error>Controller {$name} already exists.</error>");
      return Command::FAILURE;
    }

    file_put_contents($targetFile, $this->buildStub($name));

    $output->writeln("<info>Controller created: src/Http/Controllers/{$name}.php</info>");
    return Command::SUCCESS;
  }

  private function buildStub(string $name): string {
    $baseName = str_replace("Controller", "", $name);
    $serviceName = $baseName . "Service";
    $varName = lcfirst($baseName);

    return <<<PHP
    <?php

    declare(strict_types=1);

    namespace Espresso\Http\Controllers;

    use Espresso\Http\Attribute\Route;
    use Espresso\Http\Controller\AbstractController;
    use Espresso\Services\\{$serviceName};
    use Espresso\Validation\Validator;
    use Latte\Engine;
    use Psr\Http\Message\ResponseInterface;
    use Psr\Http\Message\ServerRequestInterface;

    class {$name} extends AbstractController {
      public function __construct(
        Engine \$latte,
        Validator \$validator,
        private readonly {$serviceName} \${$varName}Service,
      ) {
        parent::__construct(\$latte, \$validator);
      }

      #[Route("GET", "/")]
      public function index(ServerRequestInterface \$request): ResponseInterface {
        return \$this->view("welcome.latte");
      }
    }
    PHP;
  }
}
