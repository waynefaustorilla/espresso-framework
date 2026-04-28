<?php

declare(strict_types=1);

namespace Espresso\Container;

use DI\Container;
use DI\ContainerBuilder;
use Espresso\Container\Providers\AuthServiceProvider;
use Espresso\Container\Providers\ConsoleServiceProvider;
use Espresso\Container\Providers\DatabaseServiceProvider;
use Espresso\Container\Providers\DomainServiceProvider;
use Espresso\Container\Providers\HttpServiceProvider;
use Espresso\Container\Providers\InfrastructureServiceProvider;
use Espresso\Container\Providers\MailServiceProvider;

class ContainerFactory {
  private array $providers = [
    DatabaseServiceProvider::class,
    InfrastructureServiceProvider::class,
    AuthServiceProvider::class,
    HttpServiceProvider::class,
    MailServiceProvider::class,
    DomainServiceProvider::class,
    ConsoleServiceProvider::class,
  ];

  public function build(array $config, string $basePath): Container {
    $builder = new ContainerBuilder();

    $builder->addDefinitions([
      "config" => $config,
      "basePath" => $basePath,
    ]);

    foreach ($this->providers as $providerClass) {
      $provider = new $providerClass();
      $provider->register($builder);
    }

    return $builder->build();
  }
}
