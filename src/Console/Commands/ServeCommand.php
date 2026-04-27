<?php

declare(strict_types=1);

namespace Espresso\Console\Commands;

use Espresso\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ServeCommand extends Command {
  protected function configure(): void {
    $this
      ->setName("serve")
      ->setDescription("Start the PHP development server")
      ->addOption("host", null, InputOption::VALUE_OPTIONAL, "Host to bind to", "localhost")
      ->addOption("port", "p", InputOption::VALUE_OPTIONAL, "Port to listen on", "8000");
  }

  protected function execute(InputInterface $input, OutputInterface $output): int {
    $host = (string) $input->getOption("host");
    $port = (string) $input->getOption("port");
    $docRoot = Application::basePath("public");

    $output->writeln("<info>Development server started at http://{$host}:{$port}</info>");
    $output->writeln("<comment>Press Ctrl+C to stop.</comment>");

    passthru(sprintf("php -S %s:%s -t %s", $host, $port, escapeshellarg($docRoot)));

    return Command::SUCCESS;
  }
}