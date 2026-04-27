<?php

declare(strict_types=1);

namespace Espresso\Http\Middleware;

use Espresso\Auth\AuthManager;
use Espresso\Http\Exception\UnauthorizedException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AuthMiddleware implements MiddlewareInterface {
  public function __construct(
    private readonly AuthManager $authManager,
    private readonly string $guard = "web",
  ) {}

  public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
    $guard = $this->authManager->guard($this->guard);

    if (!$guard->check($request)) {
      throw new UnauthorizedException();
    }

    return $handler->handle($request);
  }
}
