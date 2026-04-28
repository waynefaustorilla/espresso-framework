<?php

declare(strict_types=1);

namespace Espresso\Auth\Contracts;

use Psr\Http\Message\ServerRequestInterface;

interface GuardInterface {
  public function attempt(string $email, string $password, string $userClass): bool;
  public function check(?ServerRequestInterface $request = null): bool;
  public function user(): ?Authenticatable;
  public function login(Authenticatable $user): void;
  public function logout(): void;
}
