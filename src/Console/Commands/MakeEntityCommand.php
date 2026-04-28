<?php

declare(strict_types=1);

namespace Espresso\Console\Commands;

use Espresso\Console\Generator\FileWriter;
use Espresso\Console\Generator\NamespaceResolver;
use Espresso\Console\Generator\StubBuilderInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MakeEntityCommand extends Command {
  public function __construct(
    private readonly StubBuilderInterface $stubBuilder,
    private readonly FileWriter $fileWriter,
    private readonly NamespaceResolver $namespaceResolver,
  ) {
    parent::__construct();
  }
  protected static string $defaultName = "make:entity";

  protected function configure(): void {
    $this
      ->setName("make:entity")
      ->setDescription("Create a new Doctrine entity class")
      ->addArgument("name", InputArgument::REQUIRED, "The entity class name (e.g. User)");
  }

  protected function execute(InputInterface $input, OutputInterface $output): int {
    $name = ucfirst((string) $input->getArgument("name"));
    $targetDir = $this->namespaceResolver->getAbsolutePath("entity");
    $this->fileWriter->ensureDirectory($targetDir);
    $targetFile = $targetDir . "/{$name}.php";

    if ($this->fileWriter->exists($targetFile)) {
      $output->writeln("<error>Entity {$name} already exists.</error>");
      return Command::FAILURE;
    }

    $this->fileWriter->write($targetFile, $this->stubBuilder->build($name));

    $output->writeln("<info>Entity created: src/Database/Entities/{$name}.php</info>");
    return Command::SUCCESS;
  }
}
