<?php

declare(strict_types=1);

namespace Espresso\Container\Providers;

use DI\Container;
use DI\ContainerBuilder;
use Espresso\Container\ServiceProviderInterface;
use Espresso\Http\View\ViewRenderer;
use Espresso\Mail\LogTransport;
use Espresso\Mail\Mailer;
use Espresso\Mail\MailerFactory;
use Espresso\Mail\Transport\SmtpTransportBuilder;
use Espresso\Mail\Transport\TransportFactoryInterface;
use Espresso\Mail\Transport\TransportRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\Transport\NullTransport;
use Symfony\Component\Mailer\Transport\SendmailTransport;

class MailServiceProvider implements ServiceProviderInterface {
  public function register(ContainerBuilder $builder): void {
    $builder->addDefinitions([
      SmtpTransportBuilder::class => fn() => new SmtpTransportBuilder(),

      TransportFactoryInterface::class => function (Container $c): TransportRegistry {
        $registry = new TransportRegistry();

        $smtpBuilder = $c->get(SmtpTransportBuilder::class);
        $registry->register("smtp", fn(array $config) => $smtpBuilder->build($config));
        $registry->register("smtps", fn(array $config) => $smtpBuilder->build($config));
        $registry->register("sendmail", fn(array $config) => new SendmailTransport($config["path"] ?? null));
        $registry->register("log", fn(array $config) use ($c) => new LogTransport($c->get(LoggerInterface::class)));
        $registry->register("null", fn(array $config) => new NullTransport());

        return $registry;
      },

      MailerFactory::class => function (Container $c): MailerFactory {
        return new MailerFactory($c->get(TransportFactoryInterface::class));
      },

      Mailer::class => function (Container $c): Mailer {
        return $c->get(MailerFactory::class)->create(
          $c->get("config")["mail"],
          $c->get(ViewRenderer::class),
        );
      },
    ]);
  }
}