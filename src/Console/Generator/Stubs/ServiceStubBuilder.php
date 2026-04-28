<?php

declare(strict_types=1);

namespace Espresso\Console\Generator\Stubs;

use Espresso\Console\Generator\StubBuilderInterface;

class ServiceStubBuilder implements StubBuilderInterface {
  public function build(string $name): string {
    $repositoryName = str_replace("Service", "Repository", $name);
    $repositoryInterface = $repositoryName . "Interface";

    return <<<PHP
    <?php

    declare(strict_types=1);

    namespace Espresso\Services;

    use Espresso\Database\Repository\\{$repositoryInterface};

    class {$name} {
      public function __construct(
        private readonly {$repositoryInterface} \$repository,
      ) {}
    }
    PHP;
  }
}