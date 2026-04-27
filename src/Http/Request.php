<?php

declare(strict_types=1);

namespace Espresso\Http;

use Laminas\Diactoros\ServerRequest;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;

class Request {
  public function __construct(private readonly ServerRequestInterface $psrRequest) {
  }

  public function all(): array {
    return array_merge((array) $this->psrRequest->getParsedBody(), $this->psrRequest->getQueryParams());
  }

  public function input(string $key, mixed $default = null): mixed {
    return $this->all()[$key] ?? $default;
  }

  public function query(string $key, mixed $default = null): mixed {
    return $this->psrRequest->getQueryParams()[$key] ?? $default;
  }

  public function post(string $key, mixed $default = null): mixed {
    $body = (array) $this->psrRequest->getParsedBody();

    return $body[$key] ?? $default;
  }

  public function file(string $key): ?UploadedFileInterface {
    return $this->psrRequest->getUploadedFiles()[$key] ?? null;
  }

  public function header(string $name): string {
    return $this->psrRequest->getHeaderLine($name);
  }

  public function bearerToken(): string {
    $header = $this->header("Authorization");

    if (str_starts_with($header, "Bearer ")) {
      return substr($header, 7);
    }

    return "";
  }

  public function isJson(): bool {
    return str_contains($this->header("Content-Type"), "application/json");
  }

  public function expectsJson(): bool {
    return str_contains($this->header("Accept"), "application/json");
  }

  public function method(): string {
    return $this->psrRequest->getMethod();
  }

  public function path(): string {
    return $this->psrRequest->getUri()->getPath();
  }

  public function getServerRequest(): ServerRequestInterface {
    return $this->psrRequest;
  }
}
