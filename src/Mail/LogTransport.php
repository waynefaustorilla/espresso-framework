<?php

declare(strict_types=1);

namespace Espresso\Mail;

use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Symfony\Component\Mime\RawMessage;

class LogTransport implements TransportInterface {
  public function __construct(private readonly LoggerInterface $logger) {}

  public function send(RawMessage $message, ?Envelope $envelope = null): ?SentMessage {
    $this->logger->debug("Mail sent", [
      "message" => $message->toString(),
    ]);

    return new SentMessage($message, $envelope ?? Envelope::create($message));
  }

  public function __toString(): string {
    return "log://";
  }
}