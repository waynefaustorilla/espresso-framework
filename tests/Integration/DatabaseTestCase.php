<?php

declare(strict_types=1);

namespace Tests\Integration;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Tests\TestCase;

abstract class DatabaseTestCase extends TestCase {
  protected EntityManager $entityManager;

  protected function setUp(): void {
    parent::setUp();

    $this->entityManager = $this->container->get(EntityManager::class);

    $schemaTool = new SchemaTool($this->entityManager);
    $metadata = $this->entityManager->getMetadataFactory()->getAllMetadata();
    $schemaTool->createSchema($metadata);

    $this->entityManager->beginTransaction();
  }

  protected function tearDown(): void {
    $this->entityManager->rollback();
    $this->entityManager->close();
    parent::tearDown();
  }

  protected function getEntityClasses(): array {
    return [];
  }
}
