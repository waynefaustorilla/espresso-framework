<?php

declare(strict_types=1);

if (!function_exists("basePath")) {
  function basePath(string $path = ""): string {
    return \Espresso\Application::basePath($path);
  }
}

if (!function_exists("env")) {
  function env(string $key, mixed $default = null): mixed {
    return $_ENV[$key] ?? $default;
  }
}

if (!function_exists("vite")) {
  function vite(string $entry): string {
    $manifestPath = \Espresso\Application::basePath("public/build/.vite/manifest.json");
    $isCss = str_ends_with($entry, ".css");

    if (!file_exists($manifestPath)) {
      if ($isCss) {
        return sprintf(
          '<link rel="stylesheet" href="http://localhost:5173/%s">',
          ltrim($entry, "/")
        );
      }

      return sprintf(
        '<script type="module" src="http://localhost:5173/@vite/client"></script>' . "\n" .
          '  <script type="module" src="http://localhost:5173/%s"></script>',
        ltrim($entry, "/")
      );
    }

    $manifest = json_decode((string) file_get_contents($manifestPath), true);
    $asset = $manifest[$entry] ?? null;

    if (!$asset) {
      return "";
    }

    if ($isCss) {
      return sprintf('<link rel="stylesheet" href="/build/%s">', $asset["file"]);
    }

    $html = "";

    foreach ($asset["css"] ?? [] as $cssFile) {
      $html .= sprintf('<link rel="stylesheet" href="/build/%s">' . "\n  ", $cssFile);
    }

    $html .= sprintf('<script type="module" src="/build/%s"></script>', $asset["file"]);

    return $html;
  }
}

if (!function_exists("mailer")) {
  function mailer(): \Espresso\Mail\Mailer {
    return \Espresso\Application::container()->get(\Espresso\Mail\Mailer::class);
  }
}
