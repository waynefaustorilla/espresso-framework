<?php

declare(strict_types=1);

namespace Espresso\Http\Factory;

use Espresso\Http\FormRequest;
use Espresso\Validation\Validator;
use Psr\Http\Message\ServerRequestInterface;

class FormRequestFactory {
  public function __construct(private readonly Validator $validator) {}

  public function make(string $formRequestClass, ServerRequestInterface $request): FormRequest {
    $formRequest = new $formRequestClass($this->validator);
    return $formRequest->validate($request);
  }
}