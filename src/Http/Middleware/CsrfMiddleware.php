<?php

declare(strict_types=1);

namespace Espresso\Http\Middleware;

use Espresso\Http\Exception\HttpException;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CsrfMiddleware implements MiddlewareInterface {
  private const SAFE_METHODS = ["GET", "HEAD", "OPTIONS"];
  private const TOKEN_KEY = "_csrf_token";

  public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
    if (in_array($request->getMethod(), self::SAFE_METHODS, true)) {
      return $handler->handle($request);
    }

    if ($this->isApiRequest($request)) {
      return $handler->handle($request);
    }

    $this->ensureSessionStarted();

    $token = $this->extractToken($request);

    if (!$this->validateToken($token)) {
      throw new HttpException(419, "CSRF token mismatch.");
    }

    return $handler->handle($request);
  }

  public static function generateToken(): string {
    if (session_status() !== PHP_SESSION_ACTIVE) {
      session_start();
    }

    if (empty($_SESSION[self::TOKEN_KEY])) {
      $_SESSION[self::TOKEN_KEY] = bin2hex(random_bytes(32));
    }

    return $_SESSION[self::TOKEN_KEY];
  }

  private function extractToken(ServerRequestInterface $request): string {
    $body = (array) $request->getParsedBody();

    if (!empty($body[self::TOKEN_KEY])) {
      return (string) $body[self::TOKEN_KEY];
    }

    return $request->getHeaderLine("X-CSRF-Token");
  }

  private function validateToken(string $token): bool {
    $sessionToken = $_SESSION[self::TOKEN_KEY] ?? "";
    return hash_equals($sessionToken, $token);
  }

  private function ensureSessionStarted(): void {
    if (session_status() !== PHP_SESSION_ACTIVE) {
      session_start();
    }
  }

  private function isApiRequest(ServerRequestInterface $request): bool {
    return str_starts_with($request->getUri()->getPath(), "/api/");
  }
}
