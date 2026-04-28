<?php

declare(strict_types=1);

namespace Espresso\Auth\Session;

class PhpSessionStore implements SessionStoreInterface {
  public function __construct(private readonly array $sessionConfig) {}

  public function get(string $key, mixed $default = null): mixed {
    $this->ensureSessionStarted();
    return $_SESSION[$key] ?? $default;
  }

  public function set(string $key, mixed $value): void {
    $this->ensureSessionStarted();
    $_SESSION[$key] = $value;
  }

  public function forget(string $key): void {
    $this->ensureSessionStarted();
    unset($_SESSION[$key]);
  }

  public function has(string $key): bool {
    $this->ensureSessionStarted();
    return isset($_SESSION[$key]);
  }

  public function regenerate(): void {
    $this->ensureSessionStarted();
    session_regenerate_id(true);
  }

  private function ensureSessionStarted(): void {
    if (session_status() !== PHP_SESSION_ACTIVE) {
      session_set_cookie_params([
        "lifetime" => $this->sessionConfig["lifetime"] ?? 7200,
        "path" => "/",
        "httponly" => true,
        "samesite" => "Lax",
        "secure" => isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off",
      ]);
      session_name($this->sessionConfig["cookie"] ?? "framework_session");
      session_start();
    }
  }
}