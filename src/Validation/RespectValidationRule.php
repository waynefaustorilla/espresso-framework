<?php

declare(strict_types=1);

namespace Espresso\Validation;

use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validatable;

class RespectValidationRule implements ValidationRuleInterface {
  public function __construct(private readonly Validatable $rule) {}

  public function validate(string $field, mixed $value): array {
    try {
      $this->rule->setName($field)->assert($value);
      return [];
    } catch (NestedValidationException $nestedValidationException) {
      return array_values($nestedValidationException->getMessages());
    }
  }
}