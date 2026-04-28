<?php

declare(strict_types=1);

namespace Espresso\Console\Generator;

use RuntimeException;

class FileWriter {
  public function ensureDirectory(string $path): void {
    if (!is_dir($path)) {
      mkdir($path, 0755, true);
    }
  }

  public function write(string $filePath, string $contents): void {
    if (file_exists($filePath)) {
      throw new RuntimeException("File already exists: {$filePath}");
    }

    file_put_contents($filePath, $contents);
  }

  public function exists(string $filePath): bool {
    return file_exists($filePath);
  }
}