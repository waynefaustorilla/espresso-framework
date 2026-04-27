<?php

declare(strict_types=1);

namespace Espresso\Database\Repository;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

abstract class AbstractRepository {
  protected EntityRepository $repository;

  public function __construct(protected readonly EntityManager $entityManager) {
    $this->repository = $entityManager->getRepository($this->getEntityClass());
  }

  abstract protected function getEntityClass(): string;

  public function find(int|string $id): ?object {
    return $this->repository->find($id);
  }

  public function findOrFail(int|string $id): object {
    $entity = $this->find($id);

    if ($entity === null) {
      throw new \RuntimeException(sprintf("Entity %s with id %s not found.", $this->getEntityClass(), $id));
    }

    return $entity;
  }

  public function findAll(): array {
    return $this->repository->findAll();
  }

  public function findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): array {
    return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
  }

  public function findOneBy(array $criteria): ?object {
    return $this->repository->findOneBy($criteria);
  }

  public function save(object $entity, bool $flush = true): void {
    $this->entityManager->persist($entity);

    if ($flush) {
      $this->entityManager->flush();
    }
  }

  public function delete(object $entity, bool $flush = true): void {
    $this->entityManager->remove($entity);

    if ($flush) {
      $this->entityManager->flush();
    }
  }

  public function flush(): void {
    $this->entityManager->flush();
  }
}
