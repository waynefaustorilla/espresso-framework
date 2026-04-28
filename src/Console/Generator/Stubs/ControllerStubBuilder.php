<?php

declare(strict_types=1);

namespace Espresso\Console\Generator\Stubs;

use Espresso\Console\Generator\StubBuilderInterface;

class ControllerStubBuilder implements StubBuilderInterface {
  public function build(string $name): string {
    $baseName = str_replace("Controller", "", $name);
    $serviceName = $baseName . "Service";
    $varName = lcfirst($baseName);

    return <<<PHP
    <?php

    declare(strict_types=1);

    namespace Espresso\Http\Controllers;

    use Espresso\Http\Attribute\Route;
    use Espresso\Http\Controller\AbstractController;
    use Espresso\Http\Factory\FormRequestFactory;
    use Espresso\Http\Response\ResponseFactory;
    use Espresso\Services\\{$serviceName};
    use Psr\Http\Message\ResponseInterface;
    use Psr\Http\Message\ServerRequestInterface;

    class {$name} extends AbstractController {
      public function __construct(
        ResponseFactory \$responseFactory,
        FormRequestFactory \$formRequestFactory,
        private readonly {$serviceName} \${$varName}Service,
      ) {
        parent::__construct(\$responseFactory, \$formRequestFactory);
      }

      #[Route("GET", "/")]
      public function index(ServerRequestInterface \$request): ResponseInterface {
        return \$this->view("welcome.latte");
      }
    }
    PHP;
  }
}