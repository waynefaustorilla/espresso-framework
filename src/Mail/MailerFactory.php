<?php

declare(strict_types=1);

namespace Espresso\Mail;

use Espresso\Http\View\ViewRenderer;
use Espresso\Mail\Transport\TransportFactoryInterface;
use Symfony\Component\Mailer\Mailer as SymfonyMailer;

class MailerFactory {
  public function __construct(private readonly TransportFactoryInterface $transportFactory) {}

  public function create(array $config, ViewRenderer $viewRenderer): Mailer {
    $driver = $config["default"];
    $mailerConfig = $config["mailers"][$driver] ?? $config["mailers"]["smtp"];
    $transport = $this->transportFactory->create($mailerConfig["transport"], $mailerConfig);

    return new Mailer(new SymfonyMailer($transport), $config["from"], $viewRenderer);
  }
}