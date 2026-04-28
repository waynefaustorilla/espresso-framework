<?php

declare(strict_types=1);

namespace Espresso\Auth\Guard;

use Doctrine\ORM\EntityManager;
use Espresso\Auth\Contracts\Authenticatable;
use Espresso\Auth\Contracts\GuardInterface;
use Espresso\Auth\Contracts\PasswordHasherInterface;
use Espresso\Auth\Session\PhpSessionStore;
use Espresso\Auth\Session\SessionStoreInterface;
use Psr\Http\Message\ServerRequestInterface;

class SessionGuard implements GuardInterface {
  private const USER_KEY = "_auth_user_id";
  private const USER_CLASS_KEY = "_auth_user_class";

  private ?Authenticatable $resolvedUser = null;

  public function __construct(
    private readonly EntityManager $entityManager,
    private readonly PasswordHasherInterface $passwordHasher,
    private readonly SessionStoreInterface $sessionStore,
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
    return true;
  }

  public function check(?ServerRequestInterface $request = null): bool {
    return $this->user() !== null;
  }

  public function user(): ?Authenticatable {
    if ($this->resolvedUser !== null) {
      return $this->resolvedUser;
    }

    $userId = $this->sessionStore->get(self::USER_KEY);
    $userClass = $this->sessionStore->get(self::USER_CLASS_KEY);

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
    assert($this->sessionStore instanceof PhpSessionStore);
    $this->sessionStore->regenerate();

    $this->sessionStore->set(self::USER_KEY, $user->getId());
    $this->sessionStore->set(self::USER_CLASS_KEY, get_class($user));
    $this->resolvedUser = $user;
  }

  public function logout(): void {
    assert($this->sessionStore instanceof PhpSessionStore);

    $this->sessionStore->forget(self::USER_KEY);
    $this->sessionStore->forget(self::USER_CLASS_KEY);
    $this->resolvedUser = null;

    $this->sessionStore->regenerate();
  }
}
