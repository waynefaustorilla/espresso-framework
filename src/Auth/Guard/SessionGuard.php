<?php

declare(strict_types=1);

namespace Espresso\Auth\Guard;

use Doctrine\ORM\EntityManager;
use Espresso\Auth\Contracts\Authenticatable;
use Espresso\Auth\Contracts\GuardInterface;
use Psr\Http\Message\ServerRequestInterface;

class SessionGuard implements GuardInterface {
  private const USER_KEY = "_auth_user_id";
  private const USER_CLASS_KEY = "_auth_user_class";

  private ?Authenticatable $resolvedUser = null;

  public function __construct(
    private readonly EntityManager $entityManager,
    private readonly array $config,
  ) {}

  public function check(?ServerRequestInterface $request = null): bool {
    return $this->user() !== null;
  }

  public function user(): ?Authenticatable {
    if ($this->resolvedUser !== null) {
      return $this->resolvedUser;
    }

    $this->ensureSessionStarted();

    $userId = $_SESSION[self::USER_KEY] ?? null;
    $userClass = $_SESSION[self::USER_CLASS_KEY] ?? null;

    if ($userId === null || $userClass === null) {
      return null;
    }

    $entity = $this->entityManager->find($userClass, $userId);

    if ($entity instanceof Authenticatable) {
      $this->resolvedUser = $entity;
    }

    return $this->resolvedUser;
  }

  public function login(Authenticatable $user): void {
    $this->ensureSessionStarted();
    session_regenerate_id(true);

    $_SESSION[self::USER_KEY] = $user->getId();
    $_SESSION[self::USER_CLASS_KEY] = get_class($user);
    $this->resolvedUser = $user;
  }

  public function logout(): void {
    $this->ensureSessionStarted();

    unset($_SESSION[self::USER_KEY], $_SESSION[self::USER_CLASS_KEY]);
    $this->resolvedUser = null;

    session_regenerate_id(true);
  }

  public function attempt(string $email, string $password, string $userClass): bool {
    $user = $this->entityManager->getRepository($userClass)->findOneBy(["email" => $email]);

    if (!$user instanceof Authenticatable) {
      return false;
    }

    if (!password_verify($password, $user->getPassword())) {
      return false;
    }

    $this->login($user);
    return true;
  }

  private function ensureSessionStarted(): void {
    if (session_status() !== PHP_SESSION_ACTIVE) {
      session_set_cookie_params([
        "lifetime" => $this->config["session"]["lifetime"] ?? 7200,
        "path" => "/",
        "httponly" => true,
        "samesite" => "Lax",
        "secure" => isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off",
      ]);
      session_name($this->config["session"]["cookie"] ?? "framework_session");
      session_start();
    }
  }
}
