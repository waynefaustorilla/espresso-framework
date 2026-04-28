<?php

declare(strict_types=1);

namespace Espresso\Container\Providers;

use DI\Container;
use DI\ContainerBuilder;
use Doctrine\ORM\EntityManager;
use Espresso\Auth\AuthManager;
use Espresso\Auth\Contracts\GuardInterface;
use Espresso\Auth\Contracts\PasswordHasherInterface;
use Espresso\Auth\Factory\GuardFactory;
use Espresso\Auth\Factory\GuardFactoryInterface;
use Espresso\Auth\Guard\JwtGuard;
use Espresso\Auth\Guard\SessionGuard;
use Espresso\Auth\Jwt\FirebaseJwtEncoder;
use Espresso\Auth\Jwt\JwtEncoderInterface;
use Espresso\Auth\Jwt\JwtTokenService;
use Espresso\Auth\PasswordHasher\BcryptPasswordHasher;
use Espresso\Auth\Session\PhpSessionStore;
use Espresso\Auth\Session\SessionStoreInterface;
use Espresso\Container\ServiceProviderInterface;

class AuthServiceProvider implements ServiceProviderInterface {
  public function register(ContainerBuilder $builder): void {
    $builder->addDefinitions([
      PasswordHasherInterface::class => fn() => new BcryptPasswordHasher(),

      JwtEncoderInterface::class => function (Container $c): FirebaseJwtEncoder {
        return new FirebaseJwtEncoder($c->get("config")["auth"]["guards"]["api"] ?? []);
      },

      JwtTokenService::class => function (Container $c): JwtTokenService {
        $config = $c->get("config")["auth"]["guards"]["api"] ?? [];
        return new JwtTokenService(
          $c->get(JwtEncoderInterface::class),
          (int) ($config["ttl"] ?? 3600),
          $config["issuer"] ?? "espresso",
        );
      },

      SessionStoreInterface::class => function (Container $c): PhpSessionStore {
        return new PhpSessionStore($c->get("config")["auth"]["session"] ?? []);
      },

      GuardFactoryInterface::class => function (Container $c): GuardFactory {
        $factory = new GuardFactory();

        $factory->register("web", function (array $config) use ($c): SessionGuard {
          return new SessionGuard(
            $c->get(EntityManager::class),
            $c->get(PasswordHasherInterface::class),
            $c->get(SessionStoreInterface::class),
          );
        });

        $factory->register("api", function (array $config) use ($c): JwtGuard {
          return new JwtGuard(
            $c->get(EntityManager::class),
            $c->get(PasswordHasherInterface::class),
            $c->get(JwtTokenService::class),
          );
        });

        return $factory;
      },

      AuthManager::class => function (Container $c): AuthManager {
        return new AuthManager(
          $c->get("config")["auth"],
          $c->get(GuardFactoryInterface::class),
        );
      },
    ]);
  }
}