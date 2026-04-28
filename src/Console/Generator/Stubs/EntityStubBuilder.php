<?php

declare(strict_types=1);

namespace Espresso\Console\Generator\Stubs;

use Espresso\Console\Generator\StubBuilderInterface;

class EntityStubBuilder implements StubBuilderInterface {
  public function build(string $name): string {
    $tableName = strtolower(preg_replace("/([a-z])([A-Z])/", "$1_$2", $name)) . "s";

    return <<<PHP
    <?php

    declare(strict_types=1);

    namespace Espresso\Database\Entities;

    use DateTimeImmutable;
    use Doctrine\ORM\Mapping as ORM;
    use Espresso\Database\Concerns\HasMagicProperties;

    #[ORM\Entity]
    #[ORM\HasLifecycleCallbacks]
    #[ORM\Table(name: "{$tableName}")]
    class {$name} {
      use HasMagicProperties;

      #[ORM\Id]
      #[ORM\GeneratedValue]
      #[ORM\Column(type: "integer")]
      private ?int \$id = null;

      public function __construct() {
        \$this->syncOriginal();
      }

      public function getId(): ?int {
        return \$this->id;
      }
    }
    PHP;
  }
}