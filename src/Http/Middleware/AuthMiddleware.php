<?php

declare(strict_types=1);

namespace Espresso\Http\Middleware;

use Espresso\Auth\Contracts\GuardInterface;
use Espresso\Http\Exception\UnauthorizedException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AuthMiddleware implements MiddlewareInterface {
  public function __construct(private readonly GuardInterface $guard) {}

  public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
    if (!$this->guard->check($request)) {
      throw new UnauthorizedException();
    }

    return $handler->handle($request);
  }
}
