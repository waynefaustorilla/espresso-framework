<?php

declare(strict_types=1);

namespace Espresso\Auth\Jwt;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Throwable;

class FirebaseJwtEncoder implements JwtEncoderInterface {
  public function __construct(private readonly array $config) {}

  public function encode(array $payload): string {
    return JWT::encode($payload, $this->config["secret"], $this->config["algorithm"]);
  }

  public function decode(string $token): ?array {
    try {
      $decoded = JWT::decode($token, new Key($this->config["secret"], $this->config["algorithm"]));
      return (array) $decoded;
    } catch (Throwable) {
      return null;
    }
  }
}