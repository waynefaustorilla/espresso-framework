<?php

declare(strict_types=1);

namespace Espresso\Mail\Transport;

use RuntimeException;
use Symfony\Component\Mailer\Transport\TransportInterface;

class TransportRegistry implements TransportFactoryInterface {
  private array $creators = [];

  public function register(string $driver, callable $creator): void {
    $this->creators[$driver] = $creator;
  }

  public function create(string $driver, array $config): TransportInterface {
    if (!isset($this->creators[$driver])) {
      throw new RuntimeException("Unsupported mail transport [{$driver}].");
    }

    return ($this->creators[$driver])($config);
  }
}