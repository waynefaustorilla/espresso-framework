<?php

declare(strict_types=1);

namespace Espresso\Http;

use Espresso\Auth\AuthManager;
use Espresso\Http\Middleware\ExceptionMiddleware;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Kernel {
  public function __construct(
    private readonly Router $router,
    private readonly ExceptionMiddleware $exceptionMiddleware,
    private readonly string $basePath,
    private readonly AuthManager $authManager,
  ) {
    $this->router->loadRoutes($this->basePath);
  }

  public function handle(ServerRequestInterface $request): ResponseInterface {
    $this->authManager->setRequest($request);
    $leagueRouter = $this->router->getLeagueRouter();

    $handler = new class($leagueRouter) implements RequestHandlerInterface {
      public function __construct(private readonly \League\Route\Router $router) {}

      public function handle(ServerRequestInterface $request): ResponseInterface {
        return $this->router->dispatch($request);
      }
    };

    return $this->exceptionMiddleware->process($request, $handler);
  }

  public function emit(ResponseInterface $response): void {
    (new SapiEmitter())->emit($response);
  }
}
