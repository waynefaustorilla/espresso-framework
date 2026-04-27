<?php

declare(strict_types=1);

namespace Espresso\Logging;

use Monolog\Handler\ErrorLogHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class LoggerFactory {
  public static function create(array $config): Logger {
    $defaultChannel = $config["default"];
    $channelConfig = $config["channels"][$defaultChannel] ?? $config["channels"]["file"];
    $level = self::resolveLevel($channelConfig["level"] ?? "debug");

    $logger = new Logger("framework");

    match ($channelConfig["driver"] ?? "stream") {
      "stream" => $logger->pushHandler(new StreamHandler($channelConfig["path"], $level)),
      "errorlog" => $logger->pushHandler(new ErrorLogHandler(ErrorLogHandler::OPERATING_SYSTEM, $level)),
      default => $logger->pushHandler(new StreamHandler("php://stderr", $level)),
    };

    return $logger;
  }

  private static function resolveLevel(string $level): int {
    return match (strtolower($level)) {
      "debug" => Logger::DEBUG,
      "info" => Logger::INFO,
      "notice" => Logger::NOTICE,
      "warning", "warn" => Logger::WARNING,
      "error" => Logger::ERROR,
      "critical" => Logger::CRITICAL,
      "alert" => Logger::ALERT,
      "emergency" => Logger::EMERGENCY,
      default => Logger::DEBUG,
    };
  }
}
