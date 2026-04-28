<?php

declare(strict_types=1);

namespace Espresso\Http\Response;

use Espresso\Http\View\ViewRenderer;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;

class ResponseFactory {
  public function __construct(private readonly ViewRenderer $viewRenderer) {}

  public function view(string $template, array $data = [], int $status = 200): ResponseInterface {
    return new HtmlResponse($this->viewRenderer->render($template, $data), $status);
  }

  public function json(mixed $data, int $status = 200): ResponseInterface {
    return new JsonResponse($data, $status);
  }

  public function redirect(string $url, int $status = 302): ResponseInterface {
    return new RedirectResponse($url, $status);
  }
}