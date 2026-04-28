<?php

declare(strict_types=1);

return [
  "default" => $_ENV["MAIL_MAILER"] ?? "smtp",
  "mailers" => [
    "smtp" => [
      "transport" => "smtp",
      "host" => $_ENV["MAIL_HOST"] ?? "127.0.0.1",
      "port" => (int) ($_ENV["MAIL_PORT"] ?? 587),
      "username" => $_ENV["MAIL_USERNAME"] ?? null,
      "password" => $_ENV["MAIL_PASSWORD"] ?? null,
      "encryption" => $_ENV["MAIL_ENCRYPTION"] ?? "tls",
    ],
    "sendmail" => [
      "transport" => "sendmail",
      "path" => $_ENV["MAIL_SENDMAIL_PATH"] ?? "/usr/sbin/sendmail -bs -i",
    ],
    "log" => [
      "transport" => "log",
    ],
    "null" => [
      "transport" => "null",
    ],
  ],
  "from" => [
    "address" => $_ENV["MAIL_FROM_ADDRESS"] ?? "noreply@example.com",
    "name" => $_ENV["MAIL_FROM_NAME"] ?? "Espresso",
  ],
];