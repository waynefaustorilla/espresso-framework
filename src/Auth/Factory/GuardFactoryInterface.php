<?php

declare(strict_types=1);

namespace Espresso\Auth\Factory;

use Espresso\Auth\Contracts\GuardInterface;

interface GuardFactoryInterface {
  public function create(string $driver, array $config): GuardInterface;
}