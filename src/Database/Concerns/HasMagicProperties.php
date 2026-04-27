<?php

declare(strict_types=1);

namespace Espresso\Database\Concerns;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use JsonSerializable;
use ReflectionClass;

trait HasMagicProperties {
  private array $original = [];

  #[ORM\PostLoad]
  public function syncOriginal(): void {
    $this->original = $this->extractProperties();
  }

  public function __get(string $name): mixed {
    $camel = $this->toCamelCase($name);

    foreach (["get" . ucfirst($camel), "is" . ucfirst($camel)] as $method) {
      if (method_exists($this, $method)) {
        return $this->$method();
      }
    }

    throw new InvalidArgumentException(
      sprintf("Property '%s' does not exist on %s.", $name, static::class)
    );
  }

  public function __set(string $name, mixed $value): void {
    $setter = "set" . ucfirst($this->toCamelCase($name));

    if (method_exists($this, $setter)) {
      $this->$setter($value);
      return;
    }

    throw new InvalidArgumentException(
      sprintf("Property '%s' is not settable on %s.", $name, static::class)
    );
  }

  public function __isset(string $name): bool {
    try {
      return $this->__get($name) !== null;
    } catch (InvalidArgumentException) {
      return false;
    }
  }

  public function fill(array $attributes): static {
    foreach ($attributes as $key => $value) {
      $setter = "set" . ucfirst($this->toCamelCase((string) $key));

      if (method_exists($this, $setter)) {
        $this->$setter($value);
      }
    }

    return $this;
  }

  public function toArray(): array {
    return $this->extractProperties();
  }

  public function toJson(): string {
    return json_encode($this->toArray(), JSON_THROW_ON_ERROR);
  }

  public function jsonSerialize(): array {
    return $this->toArray();
  }

  public function isDirty(?string $property = null): bool {
    if (empty($this->original)) {
      return false;
    }

    $current = $this->extractProperties();

    if ($property !== null) {
      $key = $this->toSnakeCase($property);
      return ($this->original[$key] ?? null) !== ($current[$key] ?? null);
    }

    return $this->original !== $current;
  }

  public function getOriginal(?string $property = null): mixed {
    if ($property !== null) {
      return $this->original[$this->toSnakeCase($property)] ?? null;
    }

    return $this->original;
  }

  private function extractProperties(): array {
    $result = [];
    $reflection = new ReflectionClass($this);

    foreach ($reflection->getProperties() as $property) {
      if ($property->getName() === "original") {
        continue;
      }

      $property->setAccessible(true);

      if (!$property->isInitialized($this)) {
        continue;
      }

      $value = $property->getValue($this);
      $result[$this->toSnakeCase($property->getName())] = $this->serializeValue($value);
    }

    return $result;
  }

  private function serializeValue(mixed $value): mixed {
    if ($value instanceof DateTimeInterface) {
      return $value->format(DateTimeInterface::ATOM);
    }

    if ($value instanceof JsonSerializable) {
      return $value->jsonSerialize();
    }

    if (is_object($value) && method_exists($value, "toArray")) {
      return $value->toArray();
    }

    return $value;
  }

  private function toCamelCase(string $value): string {
    return lcfirst(str_replace("_", "", ucwords($value, "_")));
  }

  private function toSnakeCase(string $value): string {
    return strtolower(preg_replace('/(?<!^)[A-Z]/', "_$0", $value));
  }
}