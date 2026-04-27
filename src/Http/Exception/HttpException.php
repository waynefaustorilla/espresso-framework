<?php

declare(strict_types=1);

namespace Espresso\Http\Exception;

use RuntimeException;

class HttpException extends RuntimeException {
  public function __construct(
    private readonly int $statusCode,
    string $message = "",
  ) {
    parent::__construct($message ?: $this->defaultMessage($statusCode));
  }

  public function getStatusCode(): int {
    return $this->statusCode;
  }

  private function defaultMessage(int $status): string {
    return match ($status) {
      400 => "Bad Request",
      401 => "Unauthorized",
      403 => "Forbidden",
      404 => "Not Found",
      405 => "Method Not Allowed",
      422 => "Unprocessable Entity",
      500 => "Internal Server Error",
      default => "HTTP Error",
    };
  }
}
