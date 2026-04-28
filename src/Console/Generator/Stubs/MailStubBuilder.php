<?php

declare(strict_types=1);

namespace Espresso\Console\Generator\Stubs;

use Espresso\Console\Generator\StubBuilderInterface;

class MailStubBuilder implements StubBuilderInterface {
  public function build(string $name): string {
    $viewName = strtolower(preg_replace("/([a-z])([A-Z])/", "$1-$2", str_replace("Mail", "", $name)));

    return <<<PHP
    <?php

    declare(strict_types=1);

    namespace Espresso\Mail;

    class {$name} extends Mailable {
      public function __construct() {}

      public function build(): static {
        return \$this
          ->subject("Subject")
          ->template("mail/{$viewName}.latte");
      }
    }
    PHP;
  }
}