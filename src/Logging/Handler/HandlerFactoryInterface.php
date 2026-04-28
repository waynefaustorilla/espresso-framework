<?php

declare(strict_types=1);

namespace Espresso\Logging\Handler;

use Monolog\Handler\HandlerInterface;

interface HandlerFactoryInterface {
  public function create(string $driver, array $config, int $level): HandlerInterface;
}