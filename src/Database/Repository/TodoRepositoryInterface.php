<?php

declare(strict_types=1);

namespace Espresso\Database\Repository;

interface TodoRepositoryInterface extends RepositoryInterface {
  public function findAllOrderedByDate(): array;
  public function findPending(): array;
  public function findCompleted(): array;
}