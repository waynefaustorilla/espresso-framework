<?php

declare(strict_types=1);

namespace Espresso\Http;

use Espresso\Validation\ValidationException;
use Espresso\Validation\Validator;
use Psr\Http\Message\ServerRequestInterface;

abstract class FormRequest {
  private array $validatedData = [];

  public function __construct(
    private readonly Validator $validator,
    private readonly DataFilter $dataFilter = new DataFilter(),
  ) {}

  abstract protected function rules(): array;

  public function validate(ServerRequestInterface $request): static {
    $data = array_merge(
      (array) $request->getParsedBody(),
      $request->getQueryParams(),
    );

    $errors = $this->validator->validate($data, $this->rules());

    if (!empty($errors)) {
      throw new ValidationException($errors);
    }

    $this->validatedData = $this->dataFilter->allowedKeys($data, array_keys($this->rules()));
    return $this;
  }

  public function validated(): array {
    return $this->validatedData;
  }

  public function get(string $key, mixed $default = null): mixed {
    return $this->validatedData[$key] ?? $default;
  }
}
