<?php

declare(strict_types=1);

namespace Tests;

use DI\Container;
use Espresso\Application;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;

abstract class TestCase extends PHPUnitTestCase {
  protected Application $app;
  protected Container $container;

  protected function setUp(): void {
    parent::setUp();
    $this->app = new Application(dirname(__DIR__));
    $this->container = $this->app->getContainer();
  }
}
