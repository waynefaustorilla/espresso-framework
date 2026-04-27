<?php

declare(strict_types=1);

namespace Espresso\Services;

use Doctrine\ORM\EntityManager;

abstract class AbstractService {
  public function __construct(protected readonly EntityManager $entityManager) {
  }
}
