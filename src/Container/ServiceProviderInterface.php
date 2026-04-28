<?php

declare(strict_types=1);

namespace Espresso\Container;

use DI\ContainerBuilder;

interface ServiceProviderInterface {
  public function register(ContainerBuilder $builder): void;
}