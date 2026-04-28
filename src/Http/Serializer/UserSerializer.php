<?php

declare(strict_types=1);

namespace Espresso\Http\Serializer;

use Espresso\Database\Entities\User;

class UserSerializer implements SerializerInterface {
  public function serialize(object $entity): array {
    assert($entity instanceof User);

    return [
      "id" => $entity->getId(),
      "name" => $entity->getName(),
      "email" => $entity->getEmail(),
      "created_at" => $entity->getCreatedAt()->format("Y-m-d H:i:s"),
    ];
  }
}