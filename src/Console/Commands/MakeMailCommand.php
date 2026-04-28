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

class MakeMailCommand extends Command {
  public function __construct(
    private readonly StubBuilderInterface $stubBuilder,
    private readonly FileWriter $fileWriter,
    private readonly NamespaceResolver $namespaceResolver,
  ) {
    parent::__construct();
  }
  protected function configure(): void {
    $this
      ->setName("make:mail")
      ->setDescription("Create a new mailable class")
      ->addArgument("name", InputArgument::REQUIRED, "The mailable class name (e.g. WelcomeMail)");
  }

  protected function execute(InputInterface $input, OutputInterface $output): int {
    $name = ucfirst((string) $input->getArgument("name"));

    if (!str_ends_with($name, "Mail")) {
      $name .= "Mail";
    }

    $targetDir = $this->namespaceResolver->getAbsolutePath("mail");
    $this->fileWriter->ensureDirectory($targetDir);
    $targetFile = $targetDir . "/{$name}.php";

    if ($this->fileWriter->exists($targetFile)) {
      $output->writeln("<error>Mailable {$name} already exists.</error>");
      return Command::FAILURE;
    }

    $this->fileWriter->write($targetFile, $this->stubBuilder->build($name));

    $output->writeln("<info>Mailable created: src/Mail/{$name}.php</info>");
    return Command::SUCCESS;
  }
}