<?php

declare(strict_types=1);

namespace Espresso\Auth\Jwt;

use Espresso\Auth\Contracts\Authenticatable;

class JwtTokenService {
  public function __construct(
    private readonly JwtEncoderInterface $encoder,
    private readonly int $ttl,
    private readonly string $issuer,
  ) {}

  public function generateToken(Authenticatable $user, string $userClass): string {
    $now = time();

    return $this->encoder->encode([
      "iss" => $this->issuer,
      "sub" => $user->getId(),
      "sub_class" => $userClass,
      "iat" => $now,
      "exp" => $now + $this->ttl,
    ]);
  }

  public function decodeToken(string $token): ?array {
    return $this->encoder->decode($token);
  }
}