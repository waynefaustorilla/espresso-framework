<?php

declare(strict_types=1);

namespace Espresso\Http\Middleware\Token;

use Psr\Http\Message\ServerRequestInterface;

class HeaderTokenExtractor implements TokenExtractorInterface {
  public function __construct(private readonly string $headerName = "X-CSRF-Token") {}

  public function extract(ServerRequestInterface $request): string {
    return $request->getHeaderLine($this->headerName);
  }
}