<?php

declare(strict_types=1);

namespace Espresso\Http\Controller;

use Espresso\Http\FormRequest;
use Espresso\Validation\ValidationException;
use Espresso\Validation\Validator;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Espresso\View\LatteFactory;
use Latte\Engine;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

abstract class AbstractController {
  public function __construct(
    protected readonly Engine $latte,
    protected readonly Validator $validator,
  ) {}

  protected function view(string $template, array $data = [], int $status = 200): ResponseInterface {
    $html = $this->latte->renderToString($template, array_merge(LatteFactory::globals(), $data));
    return new HtmlResponse($html, $status);
  }

  protected function json(mixed $data, int $status = 200): ResponseInterface {
    return new JsonResponse($data, $status);
  }

  protected function redirect(string $url, int $status = 302): ResponseInterface {
    return new RedirectResponse($url, $status);
  }

  protected function validate(array $data, array $rules): array {
    $errors = $this->validator->validate($data, $rules);

    if (!empty($errors)) {
      throw new ValidationException($errors);
    }

    return $data;
  }

  protected function formRequest(string $formRequestClass, ServerRequestInterface $request): FormRequest {
    $formRequest = new $formRequestClass($this->validator);
    return $formRequest->validate($request);
  }
}
