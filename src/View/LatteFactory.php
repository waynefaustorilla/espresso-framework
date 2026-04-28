<?php

declare(strict_types=1);

namespace Espresso\View;

use Espresso\Auth\AuthManager;
use Espresso\Http\View\ViewRenderer;
use Latte\Engine;
use Latte\Loaders\FileLoader;
use Latte\Runtime\Html;

class LatteFactory {
  public function create(array $appConfig, AuthManager $authManager, callable $csrfTokenGenerator, callable $csrfFieldGenerator): ViewRenderer {
    $latte = new Engine();
    $latte->setLoader(new FileLoader($appConfig["views"]));

    $cacheDir = $appConfig["debug"] ? null : $appConfig["storage"] . "/cache/latte";
    $latte->setTempDirectory($cacheDir);
    $latte->setAutoRefresh($appConfig["debug"]);

    $latte->addFunction("csrf_token", $csrfTokenGenerator);
    $latte->addFunction("csrf_field", $csrfFieldGenerator);

    $globals = [
      "app_name" => $appConfig["name"],
      "app_env" => $appConfig["env"],
      "auth" => $authManager,
    ];

    return new ViewRenderer($latte, $globals);
  }
}
