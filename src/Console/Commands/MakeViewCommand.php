<?php

declare(strict_types=1);

namespace Espresso\Console\Commands;

use Espresso\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MakeViewCommand extends Command {
  protected function configure(): void {
    $this
      ->setName("make:view")
      ->setDescription("Create a new Latte view template")
      ->addArgument("name", InputArgument::REQUIRED, "View name, optionally with subdirectory (e.g. posts/index or posts/create)")
      ->addOption("plain", null, InputOption::VALUE_NONE, "Generate a plain template without layout inheritance");
  }

  protected function execute(InputInterface $input, OutputInterface $output): int {
    $name = trim((string) $input->getArgument("name"), "/\\");
    $plain = (bool) $input->getOption("plain");

    if (!str_ends_with($name, ".latte")) {
      $name .= ".latte";
    }

    $targetFile = Application::basePath("resources/views/{$name}");
    $targetDir = dirname($targetFile);

    if (!is_dir($targetDir)) {
      mkdir($targetDir, 0755, true);
    }

    if (file_exists($targetFile)) {
      $output->writeln("<error>View {$name} already exists.</error>");
      return Command::FAILURE;
    }

    file_put_contents($targetFile, $plain ? $this->plainStub() : $this->layoutStub($name));

    $output->writeln("<info>View created: resources/views/{$name}</info>");
    return Command::SUCCESS;
  }

  private function layoutStub(string $name): string {
    $title = ucwords(str_replace(["/", "-", "_", ".latte"], [" ", " ", " ", ""], $name));

    return <<<LATTE
    {extends "layouts/app.latte"}

    {block title}{$title}{/block}

    {block content}
    <h1>{$title}</h1>
    {/block}
    LATTE;
  }

  private function plainStub(): string {
    return <<<LATTE
    <!DOCTYPE html>
    <html lang="en">
    <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Untitled</title>
    </head>
    <body>

    </body>
    </html>
    LATTE;
  }
}
