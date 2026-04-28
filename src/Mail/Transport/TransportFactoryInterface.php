<?php

declare(strict_types=1);

namespace Espresso\Mail\Transport;

use Symfony\Component\Mailer\Transport\TransportInterface;

interface TransportFactoryInterface {
  public function create(string $driver, array $config): TransportInterface;
}