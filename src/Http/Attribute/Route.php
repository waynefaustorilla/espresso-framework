<?php

declare(strict_types=1);

namespace Espresso\Http\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class Route {
  public function __construct(
    public readonly string $method,
    public readonly string $path,
    public readonly string $name = "",
    public readonly array $middleware = [],
  ) {}
}
