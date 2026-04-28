<?php

declare(strict_types=1);

namespace Espresso\Validation;

class Validator {
  public function validate(array $data, array $rules): array {
    $errors = [];

    foreach ($rules as $field => $rule) {
      $fieldErrors = $rule->validate($field, $data[$field] ?? null);

      if (!empty($fieldErrors)) {
        $errors[$field] = $fieldErrors;
      }
    }

    return $errors;
  }

  public function passes(array $data, array $rules): bool {
    return empty($this->validate($data, $rules));
  }
}
