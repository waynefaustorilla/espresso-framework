<?php

declare(strict_types=1);

namespace Espresso;

use DI\Container;
use Dotenv\Dotenv;
use Espresso\Console\Kernel as ConsoleKernel;
use Espresso\Container\ContainerFactory;
use Espresso\Http\Kernel as HttpKernel;
use Laminas\Diactoros\ServerRequestFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Application {
  private Container $container;
  private static string $basePath;
  private static ?Container $staticContainer = null;

  public function __construct(string $basePath) {
    self::$basePath = rtrim($basePath, DIRECTORY_SEPARATOR);
    $this->loadEnvironment();
    $config = $this->loadConfig();
    $this->container = (new ContainerFactory())->build($config, self::$basePath);
    self::$staticContainer = $this->container;
  }

  public function handleRequest(): void {
    $request = ServerRequestFactory::fromGlobals();
    $kernel = $this->container->get(HttpKernel::class);
    $response = $kernel->handle($request);
    $kernel->emit($response);
  }

  public function handleConsole(): int {
    $kernel = $this->container->get(ConsoleKernel::class);
    return $kernel->handle();
  }

  public function getContainer(): Container {
    return $this->container;
  }

  public static function container(): Container {
    if (self::$staticContainer === null) {
      throw new \RuntimeException("Application has not been initialized.");
    }
    return self::$staticContainer;
  }

  public static function basePath(string $path = ""): string {
    return $path === "" ? self::$basePath : self::$basePath . DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR);
  }

  private function loadEnvironment(): void {
    $envPath = self::$basePath;

    if (!file_exists($envPath . DIRECTORY_SEPARATOR . ".env")) {
      return;
    }

    $dotenv = Dotenv::createImmutable($envPath);
    $dotenv->load();
  }

  private function loadConfig(): array {
    $configPath = self::$basePath . DIRECTORY_SEPARATOR . "config";

    return [
      "app" => require $configPath . DIRECTORY_SEPARATOR . "app.php",
      "database" => require $configPath . DIRECTORY_SEPARATOR . "database.php",
      "cache" => require $configPath . DIRECTORY_SEPARATOR . "cache.php",
      "auth" => require $configPath . DIRECTORY_SEPARATOR . "auth.php",
      "logging" => require $configPath . DIRECTORY_SEPARATOR . "logging.php",
      "mail" => require $configPath . DIRECTORY_SEPARATOR . "mail.php",
    ];
  }
}
