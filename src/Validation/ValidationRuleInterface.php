<?php

declare(strict_types=1);

namespace Espresso\Validation;

interface ValidationRuleInterface {
  public function validate(string $field, mixed $value): array;
}