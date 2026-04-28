<?php

declare(strict_types=1);

namespace Espresso\Container\Providers;

use DI\Container;
use DI\ContainerBuilder;
use Espresso\Cache\Adapter\AdapterRegistry;
use Espresso\Cache\Adapter\AdapterFactoryInterface;
use Espresso\Cache\Adapter\RedisAdapterBuilder;
use Espresso\Cache\CacheFactory;
use Espresso\Container\ServiceProviderInterface;
use Espresso\Logging\Handler\HandlerRegistry;
use Espresso\Logging\Handler\HandlerFactoryInterface;
use Espresso\Logging\LoggerFactory;
use Monolog\Handler\ErrorLogHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Adapter\ApcuAdapter;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\CacheInterface;

class InfrastructureServiceProvider implements ServiceProviderInterface {
  public function register(ContainerBuilder $builder): void {
    $builder->addDefinitions([
      HandlerFactoryInterface::class => function (): HandlerRegistry {
        $registry = new HandlerRegistry();

        $registry->register("stream", function (array $config, int $level): StreamHandler {
          return new StreamHandler($config["path"], $level);
        });

        $registry->register("errorlog", function (array $config, int $level): ErrorLogHandler {
          return new ErrorLogHandler(ErrorLogHandler::OPERATING_SYSTEM, $level);
        });

        return $registry;
      },

      LoggerInterface::class => function (Container $c): Logger {
        return (new LoggerFactory($c->get(HandlerFactoryInterface::class)))
          ->create($c->get("config")["logging"]);
      },

      AdapterFactoryInterface::class => function (Container $c): AdapterRegistry {
        $registry = new AdapterRegistry();
        $redisBuilder = new RedisAdapterBuilder();

        $registry->register("filesystem", function (array $config, int $ttl): FilesystemAdapter {
          return new FilesystemAdapter("framework", $ttl, $config["path"]);
        });

        $registry->register("redis", function (array $config, int $ttl) use ($redisBuilder) {
          return $redisBuilder->build($config, $ttl);
        });

        $registry->register("apcu", function (array $config, int $ttl): ApcuAdapter {
          return new ApcuAdapter("framework", $ttl);
        });

        return $registry;
      },

      CacheInterface::class => function (Container $c): CacheInterface {
        return (new CacheFactory($c->get(AdapterFactoryInterface::class)))
          ->create($c->get("config")["cache"]);
      },
    ]);
  }
}