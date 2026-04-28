<?php

declare(strict_types=1);

namespace Espresso\Auth;

use Espresso\Auth\Contracts\GuardInterface;
use Espresso\Auth\Factory\GuardFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;

class AuthManager {
  private array $resolvedGuards = [];
  private ?ServerRequestInterface $request = null;

  public function __construct(
    private readonly array $config,
    private readonly GuardFactoryInterface $guardFactory,
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

    return $this->guardFactory->create($guardConfig["driver"], $guardConfig);
  }
}
