<?php

declare(strict_types=1);

namespace Espresso\Http\Middleware;

use Espresso\Auth\Session\SessionStoreInterface;
use Espresso\Http\Exception\HttpException;
use Espresso\Http\Middleware\Token\TokenExtractorInterface;
use Laminas\Diactoros\Response\JsonResponse;
use Latte\Runtime\Html;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CsrfMiddleware implements MiddlewareInterface {
  private const SAFE_METHODS = ["GET", "HEAD", "OPTIONS"];
  private const TOKEN_KEY = "_csrf_token";

  public function __construct(
    private readonly SessionStoreInterface $sessionStore,
    private readonly array $tokenExtractors,
  ) {}

  public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
    if (in_array($request->getMethod(), self::SAFE_METHODS, true)) {
      return $handler->handle($request);
    }

    if ($this->isApiRequest($request)) {
      return $handler->handle($request);
    }

    $token = $this->extractToken($request);

    if (!$this->validateToken($token)) {
      throw new HttpException(419, "CSRF token mismatch.");
    }

    return $handler->handle($request);
  }

  public function generateToken(): string {
    if (!$this->sessionStore->has(self::TOKEN_KEY)) {
      $this->sessionStore->set(self::TOKEN_KEY, bin2hex(random_bytes(32)));
    }

    return (string) $this->sessionStore->get(self::TOKEN_KEY);
  }

  public function generateField(): Html {
    $token = $this->generateToken();
    return new Html('<input type="hidden" name="_csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, "UTF-8") . '">');
  }

  private function extractToken(ServerRequestInterface $request): string {
    foreach ($this->tokenExtractors as $extractor) {
      $token = $extractor->extract($request);

      if ($token !== "") {
        return $token;
      }
    }

    return "";
  }

  private function validateToken(string $token): bool {
    $sessionToken = (string) $this->sessionStore->get(self::TOKEN_KEY, "");
    return hash_equals($sessionToken, $token);
  }

  private function isApiRequest(ServerRequestInterface $request): bool {
    return str_starts_with($request->getUri()->getPath(), "/api/");
  }
}
