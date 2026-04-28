<?php

declare(strict_types=1);

namespace Espresso\Http\Controller;

use Espresso\Http\Factory\FormRequestFactory;
use Espresso\Http\FormRequest;
use Espresso\Http\Response\ResponseFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

abstract class AbstractController {
  public function __construct(
    protected readonly ResponseFactory $responseFactory,
    protected readonly FormRequestFactory $formRequestFactory,
  ) {}

  protected function view(string $template, array $data = [], int $status = 200): ResponseInterface {
    return $this->responseFactory->view($template, $data, $status);
  }

  protected function json(mixed $data, int $status = 200): ResponseInterface {
    return $this->responseFactory->json($data, $status);
  }

  protected function redirect(string $url, int $status = 302): ResponseInterface {
    return $this->responseFactory->redirect($url, $status);
  }

  protected function formRequest(string $formRequestClass, ServerRequestInterface $request): FormRequest {
    return $this->formRequestFactory->make($formRequestClass, $request);
  }
}
