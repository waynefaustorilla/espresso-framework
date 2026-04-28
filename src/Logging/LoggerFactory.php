<?php

declare(strict_types=1);

namespace Espresso\Logging;

use Espresso\Logging\Handler\HandlerFactoryInterface;
use Monolog\Logger;

class LoggerFactory {
  public function __construct(private readonly HandlerFactoryInterface $handlerFactory) {}

  public function create(array $config): Logger {
    $defaultChannel = $config["default"];
    $channelConfig = $config["channels"][$defaultChannel] ?? $config["channels"]["file"];
    $level = $this->resolveLevel($channelConfig["level"] ?? "debug");
    $driver = $channelConfig["driver"] ?? "stream";

    $logger = new Logger("framework");
    $logger->pushHandler($this->handlerFactory->create($driver, $channelConfig, $level));

    return $logger;
  }

  private function resolveLevel(string $level): int {
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
