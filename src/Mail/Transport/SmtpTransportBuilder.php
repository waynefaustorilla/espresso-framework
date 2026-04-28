<?php

declare(strict_types=1);

namespace Espresso\Mail\Transport;

use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Transport\TransportInterface;

class SmtpTransportBuilder {
  public function build(array $config): TransportInterface {
    $scheme = ($config["encryption"] ?? "tls") === "ssl" ? "smtps" : "smtp";
    $host = $config["host"];
    $port = $config["port"];
    $username = $config["username"] ?? null;
    $password = $config["password"] ?? null;

    if ($username !== null && $password !== null) {
      $dsn = sprintf("%s://%s:%s@%s:%d", $scheme, urlencode($username), urlencode($password), $host, $port);
    } else {
      $dsn = sprintf("%s://%s:%d", $scheme, $host, $port);
    }

    return Transport::fromDsn($dsn);
  }
}