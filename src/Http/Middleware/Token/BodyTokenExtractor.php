<?php

declare(strict_types=1);

namespace Espresso\Http\Middleware\Token;

use Psr\Http\Message\ServerRequestInterface;

class BodyTokenExtractor implements TokenExtractorInterface {
  public function __construct(private readonly string $fieldName = "_csrf_token") {}

  public function extract(ServerRequestInterface $request): string {
    $body = (array) $request->getParsedBody();
    return (string) ($body[$this->fieldName] ?? "");
  }
}