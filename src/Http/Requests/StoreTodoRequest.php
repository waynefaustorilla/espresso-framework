<?php

declare(strict_types=1);

namespace Espresso\Http\Requests;

use Espresso\Http\FormRequest;
use Espresso\Validation\RespectValidationRule;
use Respect\Validation\Validator as v;

class StoreTodoRequest extends FormRequest {
  protected function rules(): array {
    return [
      "title" => new RespectValidationRule(v::stringType()->notEmpty()->length(1, 255)),
    ];
  }
}