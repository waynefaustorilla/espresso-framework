<?php

declare(strict_types=1);

namespace Espresso\Auth\Guard;

use Doctrine\ORM\EntityManager;
use Espresso\Auth\Contracts\Authenticatable;
use Espresso\Auth\Contracts\GuardInterface;
use Espresso\Auth\Contracts\PasswordHasherInterface;
use Espresso\Auth\Jwt\JwtTokenService;
use Psr\Http\Message\ServerRequestInterface;

class JwtGuard implements GuardInterface {
  private ?Authenticatable $resolvedUser = null;
  private ?string $lastGeneratedToken = null;

  public function __construct(
    private readonly EntityManager $entityManager,
    private readonly PasswordHasherInterface $passwordHasher,
    private readonly JwtTokenService $tokenService,
  ) {}

  public function attempt(string $email, string $password, string $userClass): bool {
    $user = $this->entityManager->getRepository($userClass)->findOneBy(["email" => $email]);

    if (!$user instanceof Authenticatable) {
      return false;
    }

    if (!$this->passwordHasher->verify($password, $user->getPassword())) {
      return false;
    }

    $this->login($user);
    $this->lastGeneratedToken = $this->tokenService->generateToken($user, $userClass);

    return true;
  }

  public function getToken(): ?string {
    return $this->lastGeneratedToken;
  }

  public function check(?ServerRequestInterface $request = null): bool {
    if ($request !== null) {
      return $this->resolveUserFromRequest($request) !== null;
    }

    return $this->resolvedUser !== null;
  }

  public function user(): ?Authenticatable {
    return $this->resolvedUser;
  }

  public function login(Authenticatable $user): void {
    $this->resolvedUser = $user;
  }

  public function logout(): void {
    $this->resolvedUser = null;
    $this->lastGeneratedToken = null;
  }

  private function resolveUserFromRequest(ServerRequestInterface $request): ?Authenticatable {
    if ($this->resolvedUser !== null) {
      return $this->resolvedUser;
    }

    $token = $this->extractToken($request);

    if ($token === "") {
      return null;
    }

    $payload = $this->tokenService->decodeToken($token);

    if ($payload === null) {
      return null;
    }

    $userId = $payload["sub"] ?? null;
    $userClass = $payload["sub_class"] ?? null;

    if ($userId === null || $userClass === null) {
      return null;
    }

    $entity = $this->entityManager->find($userClass, $userId);

    if ($entity instanceof Authenticatable) {
      $this->resolvedUser = $entity;
    }

    return $this->resolvedUser;
  }

  private function extractToken(ServerRequestInterface $request): string {
    $header = $request->getHeaderLine("Authorization");

    if (str_starts_with($header, "Bearer ")) {
      return substr($header, 7);
    }

    return "";
  }
}

  public function check(?ServerRequestInterface $request = null): bool {
    if ($request !== null) {
      return $this->resolveUserFromRequest($request) !== null;
    }

    return $this->resolvedUser !== null;
  }

  public function user(): ?Authenticatable {
    return $this->resolvedUser;
  }

  public function login(Authenticatable $user): void {
    $this->resolvedUser = $user;
  }

  public function logout(): void {
    $this->resolvedUser = null;
  }

  public function attempt(string $email, string $password, string $userClass): ?string {
    $user = $this->entityManager->getRepository($userClass)->findOneBy(["email" => $email]);

    if (!$user instanceof Authenticatable) {
      return null;
    }

    if (!password_verify($password, $user->getPassword())) {
      return null;
    }

    $this->login($user);

    return $this->generateToken($user, $userClass);
  }

  public function generateToken(Authenticatable $user, string $userClass): string {
    $jwtConfig = $this->config["jwt"];
    $now = time();

    $payload = [
      "iss" => $_ENV["APP_URL"] ?? "http://localhost",
      "sub" => $user->getId(),
      "sub_class" => $userClass,
      "iat" => $now,
      "exp" => $now + ($jwtConfig["ttl"] ?? 3600),
    ];

    return JWT::encode($payload, $jwtConfig["secret"], $jwtConfig["algorithm"]);
  }

  public function validateToken(string $token): ?array {
    try {
      $jwtConfig = $this->config["jwt"];
      $decoded = JWT::decode($token, new Key($jwtConfig["secret"], $jwtConfig["algorithm"]));
      return (array) $decoded;
    } catch (Throwable $throwable) {
      return null;
    }
  }

  private function resolveUserFromRequest(ServerRequestInterface $request): ?Authenticatable {
    if ($this->resolvedUser !== null) {
      return $this->resolvedUser;
    }

    $token = $this->extractToken($request);

    if ($token === "") {
      return null;
    }

    $payload = $this->validateToken($token);

    if ($payload === null) {
      return null;
    }

    $userId = $payload["sub"] ?? null;
    $userClass = $payload["sub_class"] ?? null;

    if ($userId === null || $userClass === null) {
      return null;
    }

    $entity = $this->entityManager->find($userClass, $userId);

    if ($entity instanceof Authenticatable) {
      $this->resolvedUser = $entity;
    }

    return $this->resolvedUser;
  }

  private function extractToken(ServerRequestInterface $request): string {
    $header = $request->getHeaderLine("Authorization");

    if (str_starts_with($header, "Bearer ")) {
      return substr($header, 7);
    }

    return "";
  }
}
