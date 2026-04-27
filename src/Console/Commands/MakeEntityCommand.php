<?php

declare(strict_types=1);

namespace Espresso\Console\Commands;

use Espresso\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MakeEntityCommand extends Command
{
  protected static string $defaultName = "make:entity";

  protected function configure(): void
  {
    $this
      ->setName("make:entity")
      ->setDescription("Create a new Doctrine entity class")
      ->addArgument("name", InputArgument::REQUIRED, "The entity class name (e.g. User)");
  }

  protected function execute(InputInterface $input, OutputInterface $output): int
  {
    $name = (string) $input->getArgument("name");
    $name = ucfirst($name);

    $targetDir = Application::basePath("src/Database/Entities");
    $targetFile = $targetDir . "/{$name}.php";

    if (file_exists($targetFile)) {
      $output->writeln("<error>Entity {$name} already exists.</error>");
      return Command::FAILURE;
    }

    $stub = $this->buildStub($name);
    file_put_contents($targetFile, $stub);

    $output->writeln("<info>Entity created: src/Database/Entities/{$name}.php</info>");
    return Command::SUCCESS;
  }

  private function buildStub(string $name): string
  {
    return <<<PHP
<?php

declare(strict_types=1);

namespace Espresso\Database\Entities;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Espresso\Database\Concerns\HasMagicProperties;
use JsonSerializable;

#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
#[ORM\Table(name: "{$this->toSnakeCase($name)}s")]
class {$name} implements JsonSerializable {
  use HasMagicProperties;

  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column(type: "integer")]
  private ?int \$id = null;

  public function __construct() {
    \$this->syncOriginal();
  }

  public function getId(): ?int {
    return \$this->id;
  }
}
PHP;
  }

  private function toSnakeCase(string $name): string
  {
    return strtolower(preg_replace("/[A-Z]/", "_$0", lcfirst($name)));
  }
}
