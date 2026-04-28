<?php

declare(strict_types=1);

namespace Espresso\Auth\Session;

interface SessionStoreInterface {
  public function get(string $key, mixed $default = null): mixed;
  public function set(string $key, mixed $value): void;
  public function forget(string $key): void;
  public function has(string $key): bool;
}