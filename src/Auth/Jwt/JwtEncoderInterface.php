<?php

declare(strict_types=1);

namespace Espresso\Auth\Jwt;

interface JwtEncoderInterface {
  public function encode(array $payload): string;
  public function decode(string $token): ?array;
}