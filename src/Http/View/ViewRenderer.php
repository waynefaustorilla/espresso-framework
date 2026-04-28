<?php

declare(strict_types=1);

namespace Espresso\Http\View;

use Latte\Engine;

class ViewRenderer {
  public function __construct(
    private readonly Engine $latte,
    private readonly array $globals,
  ) {}

  public function render(string $template, array $data = []): string {
    return $this->latte->renderToString($template, array_merge($this->globals, $data));
  }

  public function getEngine(): Engine {
    return $this->latte;
  }
}