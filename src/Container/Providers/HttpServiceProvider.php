<?php

declare(strict_types=1);

namespace Espresso\Container\Providers;

use DI\Container;
use DI\ContainerBuilder;
use Espresso\Auth\AuthManager;
use Espresso\Auth\Session\PhpSessionStore;
use Espresso\Auth\Session\SessionStoreInterface;
use Espresso\Container\ServiceProviderInterface;
use Espresso\Http\Controller\AbstractController;
use Espresso\Http\Exception\Handler\GenericExceptionHandler;
use Espresso\Http\Exception\Handler\HttpExceptionHandler;
use Espresso\Http\Exception\Handler\ValidationExceptionHandler;
use Espresso\Http\Factory\FormRequestFactory;
use Espresso\Http\Kernel as HttpKernel;
use Espresso\Http\Middleware\AuthMiddleware;
use Espresso\Http\Middleware\CsrfMiddleware;
use Espresso\Http\Middleware\ExceptionMiddleware;
use Espresso\Http\Middleware\Token\BodyTokenExtractor;
use Espresso\Http\Middleware\Token\HeaderTokenExtractor;
use Espresso\Http\Response\ResponseFactory;
use Espresso\Http\Router;
use Espresso\Http\Serializer\TodoSerializer;
use Espresso\Http\Serializer\UserSerializer;
use Espresso\Http\View\ViewRenderer;
use Espresso\Services\TodoService;
use Espresso\Services\TodoTransformer;
use Espresso\Validation\Validator;
use Espresso\View\LatteFactory;
use Psr\Log\LoggerInterface;

class HttpServiceProvider implements ServiceProviderInterface {
  public function register(ContainerBuilder $builder): void {
    $builder->addDefinitions([
      Validator::class => fn() => new Validator(),

      TodoSerializer::class => fn() => new TodoSerializer(),
      UserSerializer::class => fn() => new UserSerializer(),

      CsrfMiddleware::class => function (Container $c): CsrfMiddleware {
        return new CsrfMiddleware(
          $c->get(SessionStoreInterface::class),
          [new HeaderTokenExtractor(), new BodyTokenExtractor()],
        );
      },

      ViewRenderer::class => function (Container $c): ViewRenderer {
        $csrfMiddleware = $c->get(CsrfMiddleware::class);
        $latteFactory = new LatteFactory();

        return $latteFactory->create(
          $c->get("config")["app"],
          $c->get(AuthManager::class),
          fn(): string => $csrfMiddleware->generateToken(),
          fn() => $csrfMiddleware->generateField(),
        );
      },

      ResponseFactory::class => function (Container $c): ResponseFactory {
        return new ResponseFactory($c->get(ViewRenderer::class));
      },

      FormRequestFactory::class => function (Container $c): FormRequestFactory {
        return new FormRequestFactory($c->get(Validator::class));
      },

      ExceptionMiddleware::class => function (Container $c): ExceptionMiddleware {
        $viewRenderer = $c->get(ViewRenderer::class);
        $logger = $c->get(LoggerInterface::class);
        $debug = $c->get("config")["app"]["debug"];

        return new ExceptionMiddleware([
          new ValidationExceptionHandler($viewRenderer),
          new HttpExceptionHandler($viewRenderer),
          new GenericExceptionHandler($viewRenderer, $logger, $debug),
        ]);
      },

      Router::class => function (Container $c): Router {
        return new Router($c);
      },

      HttpKernel::class => function (Container $c): HttpKernel {
        return new HttpKernel(
          $c->get(Router::class),
          $c->get(ExceptionMiddleware::class),
          $c->get("basePath"),
          $c->get(AuthManager::class),
        );
      },
    ]);
  }
}