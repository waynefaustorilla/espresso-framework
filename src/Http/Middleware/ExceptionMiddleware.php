<?php

declare(strict_types=1);

namespace Espresso\Http\Middleware;

use Espresso\Http\Exception\Handler\ExceptionHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;

class ExceptionMiddleware implements MiddlewareInterface {
  public function __construct(
    private readonly array $handlers,
  ) {}

  public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
    try {
      return $handler->handle($request);
    } catch (Throwable $exception) {
      return $this->resolveHandler($exception)->handle($exception, $request);
    }
  }

  private function resolveHandler(Throwable $exception): ExceptionHandlerInterface {
    foreach ($this->handlers as $handler) {
      if ($handler->canHandle($exception)) {
        return $handler;
      }
    }

    throw new \RuntimeException("No exception handler found for " . get_class($exception));
  }
}
