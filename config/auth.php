<?php

declare(strict_types=1);

return [
  "default" => $_ENV["AUTH_GUARD"] ?? "web",
  "guards" => [
    "web" => [
      "driver" => "session",
    ],
    "api" => [
      "driver" => "jwt",
    ],
  ],
  "jwt" => [
    "secret" => $_ENV["JWT_SECRET"] ?? "",
    "algorithm" => "HS256",
    "ttl" => (int) ($_ENV["JWT_TTL"] ?? 3600),
  ],
  "session" => [
    "lifetime" => (int) ($_ENV["SESSION_LIFETIME"] ?? 7200),
    "cookie" => $_ENV["SESSION_COOKIE"] ?? "framework_session",
  ],
];
