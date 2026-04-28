<?php

declare(strict_types=1);

namespace Espresso\Services;

use Espresso\Database\Entities\Todo;
use Espresso\Database\Entities\User;

class TodoTransformer {
  public function fromArray(array $data, User $user): Todo {
    return new Todo($data["title"], $user);
  }

  public function applyUpdate(Todo $todo, array $data): Todo {
    $todo->setTitle($data["title"]);
    return $todo;
  }
}
