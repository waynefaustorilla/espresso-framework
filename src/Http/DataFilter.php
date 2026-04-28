<?php

declare(strict_types=1);

namespace Espresso\Http;

class DataFilter {
  public function allowedKeys(array $data, array $allowedKeys): array {
    return array_intersect_key($data, array_flip($allowedKeys));
  }
}