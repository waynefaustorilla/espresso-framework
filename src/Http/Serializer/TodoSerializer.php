<?php

declare(strict_types=1);

namespace Espresso\Http\Serializer;

use Espresso\Database\Entities\Todo;

class TodoSerializer implements SerializerInterface {
  public function serialize(object $entity): array {
    assert($entity instanceof Todo);

    return [
      "id" => $entity->getId(),
      "title" => $entity->getTitle(),
      "completed" => $entity->isCompleted(),
      "created_at" => $entity->getCreatedAt()->format("Y-m-d H:i:s"),
    ];
  }
}