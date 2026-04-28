<?php

declare(strict_types=1);

namespace Espresso\Mail;

use Espresso\Http\View\ViewRenderer;
use RuntimeException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class Mailer {
  public function __construct(
    private readonly MailerInterface $transport,
    private readonly array $fromConfig,
    private readonly ViewRenderer $viewRenderer,
  ) {}

  public function send(Mailable $mailable): void {
    $mailable->build();

    $email = new Email();

    $this->applyFrom($email, $mailable);
    $this->applyRecipients($email, $mailable);
    $this->applyContent($email, $mailable);

    $this->transport->send($email);
  }

  private function applyFrom(Email $email, Mailable $mailable): void {
    $from = $mailable->getFrom();

    if (!empty($from)) {
      $email->from(new Address($from["address"], $from["name"]));
      return;
    }

    $email->from(new Address($this->fromConfig["address"], $this->fromConfig["name"]));
  }

  private function applyRecipients(Email $email, Mailable $mailable): void {
    $to = $mailable->getTo();

    if (empty($to)) {
      throw new RuntimeException("Mail must have at least one recipient.");
    }

    foreach ($to as $recipient) {
      $email->addTo(new Address($recipient["address"], $recipient["name"]));
    }

    foreach ($mailable->getCc() as $recipient) {
      $email->addCc(new Address($recipient["address"], $recipient["name"]));
    }

    foreach ($mailable->getBcc() as $recipient) {
      $email->addBcc(new Address($recipient["address"], $recipient["name"]));
    }

    foreach ($mailable->getReplyTo() as $recipient) {
      $email->addReplyTo(new Address($recipient["address"], $recipient["name"]));
    }
  }

  private function applyContent(Email $email, Mailable $mailable): void {
    $subject = $mailable->getSubject();

    if ($subject !== "") {
      $email->subject($subject);
    }

    $template = $mailable->getTemplate();
    $html = $template !== null
      ? $this->viewRenderer->render($template, $mailable->getTemplateData())
      : $mailable->getHtmlBody();

    $text = $mailable->getTextBody();

    if ($html !== "") {
      $email->html($html);
    }

    if ($text !== "") {
      $email->text($text);
    }
  }
}