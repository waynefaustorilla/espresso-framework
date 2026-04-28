<?php

declare(strict_types=1);

namespace Espresso\Console\Generator\Stubs;

use Espresso\Console\Generator\StubBuilderInterface;

class RepositoryStubBuilder implements StubBuilderInterface {
  public function build(string $name): string {
    $entityName = str_replace("Repository", "", $name);

    return <<<PHP
    <?php

    declare(strict_types=1);

    namespace Espresso\Database\Repository;

    use Espresso\Database\Entities\\{$entityName};

    class {$name} extends AbstractRepository implements {$name}Interface {
      protected function getEntityClass(): string {
        return {$entityName}::class;
      }
    }
    PHP;
  }
}