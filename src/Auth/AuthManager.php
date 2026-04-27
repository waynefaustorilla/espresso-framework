<?php

declare(strict_types=1);

namespace Espresso\Auth;

use Doctrine\ORM\EntityManager;
use Espresso\Auth\Contracts\GuardInterface;
use Espresso\Auth\Guard\JwtGuard;
use Espresso\Auth\Guard\SessionGuard;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;

class AuthManager {
  private array $resolvedGuards = [];
  private ?ServerRequestInterface $request = null;

  public function __construct(
    private readonly array $config,
    private readonly EntityManager $entityManager,
  ) {}

  public function guard(string $name = ""): GuardInterface {
    $name = $name ?: $this->config["default"];

    if (isset($this->resolvedGuards[$name])) {
      return $this->resolvedGuards[$name];
    }

    $this->resolvedGuards[$name] = $this->createGuard($name);
    return $this->resolvedGuards[$name];
  }

  public function setRequest(ServerRequestInterface $request): void {
    $this->request = $request;
  }

  public function check(): bool {
    return $this->guard()->check($this->request);
  }

  private function createGuard(string $name): GuardInterface {
    $guardConfig = $this->config["guards"][$name] ?? null;

    if ($guardConfig === null) {
      throw new RuntimeException("Auth guard [{$name}] is not defined.");
    }

    return match ($guardConfig["driver"]) {
      "session" => new SessionGuard($this->entityManager, $this->config),
      "jwt" => new JwtGuard($this->entityManager, $this->config),
      default => throw new RuntimeException("Unsupported auth driver [{$guardConfig['driver']}]."),
    };
  }
}
