<?php

declare(strict_types=1);

namespace Espresso\Console\Generator\Stubs;

use Espresso\Console\Generator\StubBuilderInterface;

class RequestStubBuilder implements StubBuilderInterface {
  public function build(string $name): string {
    return <<<PHP
    <?php

    declare(strict_types=1);

    namespace Espresso\Http\Requests;

    use Espresso\Http\FormRequest;
    use Espresso\Validation\RespectValidationRule;
    use Respect\Validation\Validator as v;

    class {$name} extends FormRequest {
      protected function rules(): array {
        return [
        ];
      }
    }
    PHP;
  }
}