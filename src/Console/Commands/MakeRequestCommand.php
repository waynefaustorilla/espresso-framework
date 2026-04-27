<?php

declare(strict_types=1);

namespace Espresso\Console\Commands;

use Espresso\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MakeRequestCommand extends Command {
  protected function configure(): void {
    $this
      ->setName("make:request")
      ->setDescription("Create a new FormRequest class")
      ->addArgument("name", InputArgument::REQUIRED, "The FormRequest class name (e.g. StoreUserRequest)");
  }

  protected function execute(InputInterface $input, OutputInterface $output): int {
    $name = ucfirst((string) $input->getArgument("name"));

    if (!str_ends_with($name, "Request")) {
      $name .= "Request";
    }

    $targetDir = Application::basePath("src/Http/Requests");

    if (!is_dir($targetDir)) {
      mkdir($targetDir, 0755, true);
    }

    $targetFile = $targetDir . "/{$name}.php";

    if (file_exists($targetFile)) {
      $output->writeln("<error>Request {$name} already exists.</error>");
      return Command::FAILURE;
    }

    file_put_contents($targetFile, $this->buildStub($name));

    $output->writeln("<info>Request created: src/Http/Requests/{$name}.php</info>");
    return Command::SUCCESS;
  }

  private function buildStub(string $name): string {
    return <<<PHP
    <?php

    declare(strict_types=1);

    namespace Espresso\Http\Requests;

    use Espresso\Http\FormRequest;
    use Respect\Validation\Validator as v;

    class {$name} extends FormRequest {
      protected function rules(): array {
        return [
          // "field" => v::notEmpty()->stringType()->length(1, 255),
        ];
      }
    }
    PHP;
  }
}
