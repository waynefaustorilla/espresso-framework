<?php

declare(strict_types=1);

namespace Espresso\Http\Exception\Handler;

use Espresso\Http\View\ViewRenderer;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Throwable;

class GenericExceptionHandler implements ExceptionHandlerInterface {
  public function __construct(
    private readonly ViewRenderer $viewRenderer,
    private readonly LoggerInterface $logger,
    private readonly bool $debug,
  ) {}

  public function canHandle(Throwable $exception): bool {
    return true;
  }

  public function handle(Throwable $exception, ServerRequestInterface $request): ResponseInterface {
    $this->logger->error($exception->getMessage(), ["exception" => $exception]);

    if ($this->expectsJson($request)) {
      $payload = ["message" => "Internal Server Error"];

      if ($this->debug) {
        $payload["debug"] = [
          "exception" => get_class($exception),
          "message" => $exception->getMessage(),
          "trace" => $exception->getTraceAsString(),
        ];
      }

      return new JsonResponse($payload, 500);
    }

    return new HtmlResponse(
      $this->viewRenderer->render("errors/500.latte", [
        "message" => $this->debug ? $exception->getMessage() : "Internal Server Error",
        "trace" => $this->debug ? $exception->getTraceAsString() : null,
      ]),
      500,
    );
  }

  private function expectsJson(ServerRequestInterface $request): bool {
    return str_contains($request->getHeaderLine("Accept"), "application/json");
  }
}