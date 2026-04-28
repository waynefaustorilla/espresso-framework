<?php

declare(strict_types=1);

namespace Espresso\Http\Exception\Handler;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

interface ExceptionHandlerInterface {
  public function canHandle(Throwable $exception): bool;
  public function handle(Throwable $exception, ServerRequestInterface $request): ResponseInterface;
}