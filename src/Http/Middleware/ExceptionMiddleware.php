<?php

declare(strict_types=1);

namespace Espresso\Http\Middleware;

use Espresso\Http\Exception\HttpException;
use Espresso\Validation\ValidationException;
use League\Route\Http\Exception as LeagueHttpException;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Espresso\View\LatteFactory;
use Latte\Engine;
use Psr\Log\LoggerInterface;
use Throwable;

class ExceptionMiddleware implements MiddlewareInterface {
  public function __construct(
    private readonly Engine $latte,
    private readonly LoggerInterface $logger,
    private readonly bool $debug,
  ) {}

  public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
    try {
      return $handler->handle($request);
    } catch (ValidationException $validationException) {
      return $this->handleValidationException($request, $validationException);
    } catch (HttpException $httpException) {
      return $this->handleHttpException($request, $httpException->getStatusCode(), $httpException->getMessage());
    } catch (LeagueHttpException $leagueHttpException) {
      return $this->handleHttpException($request, $leagueHttpException->getStatusCode(), $leagueHttpException->getMessage());
    } catch (Throwable $throwable) {
      $this->logger->error($throwable->getMessage(), ["exception" => $throwable]);
      return $this->handleServerError($request, $throwable);
    }
  }

  private function handleValidationException(ServerRequestInterface $request, ValidationException $validationException): ResponseInterface {
    if ($this->expectsJson($request)) {
      return new JsonResponse(["errors" => $validationException->getErrors()], 422);
    }

    return new HtmlResponse(
      $this->latte->renderToString("errors/422.latte", array_merge(LatteFactory::globals(), ["errors" => $validationException->getErrors()])),
      422,
    );
  }

  private function handleHttpException(ServerRequestInterface $request, int $status, string $message): ResponseInterface {
    if ($this->expectsJson($request)) {
      return new JsonResponse(["message" => $message], $status);
    }

    $template = "errors/{$status}.latte";

    try {
      $html = $this->latte->renderToString($template, array_merge(LatteFactory::globals(), ["message" => $message]));
    } catch (Throwable) {
      $html = $this->latte->renderToString("errors/500.latte", array_merge(LatteFactory::globals(), ["message" => $message]));
    }

    return new HtmlResponse($html, $status);
  }

  private function handleServerError(ServerRequestInterface $request, Throwable $throwable): ResponseInterface {
    if ($this->expectsJson($request)) {
      $payload = ["message" => "Internal Server Error"];
      if ($this->debug) {
        $payload["debug"] = ["exception" => get_class($throwable), "message" => $throwable->getMessage(), "trace" => $throwable->getTraceAsString()];
      }
      return new JsonResponse($payload, 500);
    }

    $html = $this->latte->renderToString("errors/500.latte", array_merge(LatteFactory::globals(), [
      "message" => $this->debug ? $throwable->getMessage() : "Internal Server Error",
      "trace" => $this->debug ? $throwable->getTraceAsString() : null,
    ]));

    return new HtmlResponse($html, 500);
  }

  private function expectsJson(ServerRequestInterface $request): bool {
    return str_contains($request->getHeaderLine("Accept"), "application/json");
  }
}
