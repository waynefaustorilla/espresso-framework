<?php

declare(strict_types=1);

namespace Espresso\Http\Exception\Handler;

use Espresso\Http\Exception\HttpException;
use Espresso\Http\View\ViewRenderer;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\JsonResponse;
use League\Route\Http\Exception as LeagueHttpException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

class HttpExceptionHandler implements ExceptionHandlerInterface {
  public function __construct(private readonly ViewRenderer $viewRenderer) {}

  public function canHandle(Throwable $exception): bool {
    return $exception instanceof HttpException || $exception instanceof LeagueHttpException;
  }

  public function handle(Throwable $exception, ServerRequestInterface $request): ResponseInterface {
    $status = $exception instanceof HttpException
      ? $exception->getStatusCode()
      : $exception->getStatusCode();

    $message = $exception->getMessage();

    if ($this->expectsJson($request)) {
      return new JsonResponse(["message" => $message], $status);
    }

    $template = "errors/{$status}.latte";
    $fallback = "errors/500.latte";

    try {
      $html = $this->viewRenderer->render($template, ["message" => $message]);
    } catch (Throwable) {
      $html = $this->viewRenderer->render($fallback, ["message" => $message]);
    }

    return new HtmlResponse($html, $status);
  }

  private function expectsJson(ServerRequestInterface $request): bool {
    return str_contains($request->getHeaderLine("Accept"), "application/json");
  }
}