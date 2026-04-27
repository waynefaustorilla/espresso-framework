<?php

declare(strict_types=1);

namespace Espresso\Auth\Contracts;

interface Authenticatable {
  public function getId(): int|string;
  public function getEmail(): string;
  public function getPassword(): string;
}
