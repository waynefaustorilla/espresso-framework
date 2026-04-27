<?php

declare(strict_types=1);

namespace Espresso\Http;

use DI\Container;
use Espresso\Http\Attribute\Route as RouteAttribute;
use League\Route\Router as LeagueRouter;
use League\Route\Strategy\ApplicationStrategy;
use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionMethod;

class Router {
  private LeagueRouter $router;

  public function __construct(private readonly ContainerInterface $container) {
    $strategy = new ApplicationStrategy();
    $strategy->setContainer($container);

    $this->router = new LeagueRouter();
    $this->router->setStrategy($strategy);
  }

  public function loadRoutes(string $basePath): void {
    $router = $this->router;
    $container = $this->container;

    require $basePath . "/routes/web.php";
    require $basePath . "/routes/api.php";
  }

  public function scanControllers(array $controllerClasses): void {
    foreach ($controllerClasses as $controllerClass) {
      $this->registerAttributeRoutes($controllerClass);
    }
  }

  public function getLeagueRouter(): LeagueRouter {
    return $this->router;
  }

  private function registerAttributeRoutes(string $controllerClass): void {
    $reflection = new ReflectionClass($controllerClass);

    foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
      $attributes = $method->getAttributes(RouteAttribute::class);

      foreach ($attributes as $attribute) {
        $route = $attribute->newInstance();

        $this->router->map($route->method, $route->path, [$controllerClass, $method->getName()]);
      }
    }
  }
}
