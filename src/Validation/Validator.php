<?php

declare(strict_types=1);

namespace Espresso\Validation;

use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator as v;

class Validator {
  public function validate(array $data, array $rules): array {
    $errors = [];

    foreach ($rules as $field => $rule) {
      $value = $data[$field] ?? null;

      try {
        $rule->setName($field)->assert($value);
      } catch (NestedValidationException $nestedValidationException) {
        $errors[$field] = array_values($nestedValidationException->getMessages());
      }
    }

    return $errors;
  }

  public function passes(array $data, array $rules): bool {
    return empty($this->validate($data, $rules));
  }
}
