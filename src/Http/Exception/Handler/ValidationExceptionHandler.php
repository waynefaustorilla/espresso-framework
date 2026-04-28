<?php

declare(strict_types=1);

namespace Espresso\Http\Exception\Handler;

use Espresso\Http\View\ViewRenderer;
use Espresso\Validation\ValidationException;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

class ValidationExceptionHandler implements ExceptionHandlerInterface {
  public function __construct(private readonly ViewRenderer $viewRenderer) {}

  public function canHandle(Throwable $exception): bool {
    return $exception instanceof ValidationException;
  }

  public function handle(Throwable $exception, ServerRequestInterface $request): ResponseInterface {
    assert($exception instanceof ValidationException);

    if ($this->expectsJson($request)) {
      return new JsonResponse(["errors" => $exception->getErrors()], 422);
    }

    return new HtmlResponse(
      $this->viewRenderer->render("errors/422.latte", ["errors" => $exception->getErrors()]),
      422,
    );
  }

  private function expectsJson(ServerRequestInterface $request): bool {
    return str_contains($request->getHeaderLine("Accept"), "application/json");
  }
}