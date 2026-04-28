<?php

declare(strict_types=1);

namespace Espresso\Auth\Contracts;

interface PasswordHasherInterface {
  public function hash(string $plainPassword): string;
  public function verify(string $plainPassword, string $hashedPassword): bool;
}