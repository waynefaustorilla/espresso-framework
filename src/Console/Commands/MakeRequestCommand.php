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

class MakeRequestCommand extends Command {
  public function __construct(
    private readonly StubBuilderInterface $stubBuilder,
    private readonly FileWriter $fileWriter,
    private readonly NamespaceResolver $namespaceResolver,
  ) {
    parent::__construct();
  }
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

    $targetDir = $this->namespaceResolver->getAbsolutePath("request");
    $this->fileWriter->ensureDirectory($targetDir);
    $targetFile = $targetDir . "/{$name}.php";

    if ($this->fileWriter->exists($targetFile)) {
      $output->writeln("<error>Request {$name} already exists.</error>");
      return Command::FAILURE;
    }

    $this->fileWriter->write($targetFile, $this->stubBuilder->build($name));

    $output->writeln("<info>Request created: src/Http/Requests/{$name}.php</info>");
    return Command::SUCCESS;
  }
}
