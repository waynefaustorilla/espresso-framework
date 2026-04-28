<?php

declare(strict_types=1);

namespace Espresso\Auth\PasswordHasher;

use Espresso\Auth\Contracts\PasswordHasherInterface;

class BcryptPasswordHasher implements PasswordHasherInterface {
  public function hash(string $plainPassword): string {
    return password_hash($plainPassword, PASSWORD_BCRYPT);
  }

  public function verify(string $plainPassword, string $hashedPassword): bool {
    return password_verify($plainPassword, $hashedPassword);
  }
}