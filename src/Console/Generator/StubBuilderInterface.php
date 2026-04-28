<?php

declare(strict_types=1);

namespace Espresso\Console\Generator;

interface StubBuilderInterface {
  public function build(string $name): string;
}
