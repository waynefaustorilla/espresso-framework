<?php

declare(strict_types=1);

namespace Espresso\Database\Repository;

use Espresso\Database\Entities\Todo;

class TodoRepository extends AbstractRepository {
  protected function getEntityClass(): string {
    return Todo::class;
  }

  public function findAllOrderedByDate(): array {
    return $this->repository
      ->createQueryBuilder("t")
      ->orderBy("t.createdAt", "DESC")
      ->getQuery()
      ->getResult();
  }

  public function findPending(): array {
    return $this->findBy(["completed" => false]);
  }

  public function findCompleted(): array {
    return $this->findBy(["completed" => true]);
  }
}