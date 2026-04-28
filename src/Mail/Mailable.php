<?php

declare(strict_types=1);

namespace Espresso\Mail;

abstract class Mailable {
  private string $subject = "";
  private string $htmlBody = "";
  private string $textBody = "";
  private array $to = [];
  private array $from = [];
  private array $cc = [];
  private array $bcc = [];
  private array $replyTo = [];
  private ?string $template = null;
  private array $templateData = [];

  abstract public function build(): static;

  public function to(string $address, string $name = ""): static {
    $this->to[] = ["address" => $address, "name" => $name];

    return $this;
  }

  public function from(string $address, string $name = ""): static {
    $this->from = ["address" => $address, "name" => $name];

    return $this;
  }

  public function subject(string $subject): static {
    $this->subject = $subject;

    return $this;
  }

  public function html(string $body): static {
    $this->htmlBody = $body;

    return $this;
  }

  public function view(string $template, array $data = []): static {
    $this->template = $template;
    $this->templateData = $data;

    return $this;
  }

  public function text(string $body): static {
    $this->textBody = $body;

    return $this;
  }

  public function cc(string $address, string $name = ""): static {
    $this->cc[] = ["address" => $address, "name" => $name];

    return $this;
  }

  public function bcc(string $address, string $name = ""): static {
    $this->bcc[] = ["address" => $address, "name" => $name];

    return $this;
  }

  public function replyTo(string $address, string $name = ""): static {
    $this->replyTo[] = ["address" => $address, "name" => $name];

    return $this;
  }

  public function getSubject(): string {
    return $this->subject;
  }

  public function getHtmlBody(): string {
    return $this->htmlBody;
  }

  public function getTextBody(): string {
    return $this->textBody;
  }

  public function getTo(): array {
    return $this->to;
  }

  public function getFrom(): array {
    return $this->from;
  }

  public function getCc(): array {
    return $this->cc;
  }

  public function getBcc(): array {
    return $this->bcc;
  }

  public function getReplyTo(): array {
    return $this->replyTo;
  }

  public function getTemplate(): ?string {
    return $this->template;
  }

  public function getTemplateData(): array {
    return $this->templateData;
  }
}