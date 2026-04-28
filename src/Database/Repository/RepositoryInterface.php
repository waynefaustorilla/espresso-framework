<?php

declare(strict_types=1);

namespace Espresso\Database\Repository;

interface RepositoryInterface {
  public function find(int|string $id): ?object;
  public function findOrFail(int|string $id): object;
  public function findAll(): array;
  public function findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): array;
  public function findOneBy(array $criteria): ?object;
  public function save(object $entity, bool $flush = true): void;
  public function delete(object $entity, bool $flush = true): void;
  public function flush(): void;
}