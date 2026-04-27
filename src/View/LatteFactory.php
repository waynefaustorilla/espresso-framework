<?php

declare(strict_types=1);

namespace Espresso\View;

use Espresso\Auth\AuthManager;
use Espresso\Http\Middleware\CsrfMiddleware;
use Latte\Engine;
use Latte\Loaders\FileLoader;
use Latte\Runtime\Html;

class LatteFactory {
  private static array $globals = [];

  public static function create(array $appConfig, AuthManager $authManager): Engine {
    $latte = new Engine();
    $latte->setLoader(new FileLoader($appConfig["views"]));

    $cacheDir = $appConfig["debug"] ? null : $appConfig["storage"] . "/cache/latte";
    $latte->setTempDirectory($cacheDir);
    $latte->setAutoRefresh($appConfig["debug"]);

    self::$globals = [
      "app_name" => $appConfig["name"],
      "app_env"  => $appConfig["env"],
      "auth"     => $authManager,
    ];

    $latte->addFunction("csrf_token", function (): string {
      return CsrfMiddleware::generateToken();
    });

    $latte->addFunction("csrf_field", function (): Html {
      $token = CsrfMiddleware::generateToken();
      return new Html('<input type="hidden" name="_csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, "UTF-8") . '">');
    });

    return $latte;
  }

  public static function globals(): array {
    return self::$globals;
  }
}
