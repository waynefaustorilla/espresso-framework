<?php

declare(strict_types=1);

namespace Espresso\Http\Serializer;

interface SerializerInterface {
  public function serialize(object $entity): array;
}