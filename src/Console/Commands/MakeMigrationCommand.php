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

class MakeMigrationCommand extends Command {
  public function __construct(
    private readonly StubBuilderInterface $stubBuilder,
    private readonly FileWriter $fileWriter,
    private readonly NamespaceResolver $namespaceResolver,
  ) {
    parent::__construct();
  }
  protected function configure(): void {
    $this
      ->setName("make:migration")
      ->setDescription("Create a new blank Doctrine migration file")
      ->addArgument("name", InputArgument::REQUIRED, "Descriptive name in PascalCase (e.g. CreateUsersTable)");
  }

  protected function execute(InputInterface $input, OutputInterface $output): int {
    $name = ucfirst((string) $input->getArgument("name"));
    $timestamp = date("YmdHis");
    $fileName = "{$timestamp}_{$name}";
    $targetDir = $this->namespaceResolver->getAbsolutePath("migration");
    $this->fileWriter->ensureDirectory($targetDir);
    $targetFile = $targetDir . "/{$fileName}.php";

    $this->fileWriter->write($targetFile, $this->stubBuilder->build($name));

    $output->writeln("<info>Migration created: database/migrations/{$fileName}.php</info>");
    return Command::SUCCESS;
  }
}
