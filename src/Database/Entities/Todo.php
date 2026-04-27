<?php

declare(strict_types=1);

namespace Espresso\Database\Entities;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Espresso\Database\Concerns\HasMagicProperties;
use JsonSerializable;

#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
#[ORM\Table(name: "todos")]
class Todo implements JsonSerializable {
  use HasMagicProperties;

  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column(type: "integer")]
  private ?int $id = null;

  #[ORM\Column(type: "string", length: 255)]
  private string $title;

  #[ORM\Column(type: "boolean")]
  private bool $completed = false;

  #[ORM\Column(type: "datetime_immutable")]
  private DateTimeImmutable $createdAt;

  #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "todos")]
  #[ORM\JoinColumn(nullable: false)]
  private User $user;

  public function __construct(string $title, User $user) {
    $this->title = $title;
    $this->user = $user;
    $this->createdAt = new DateTimeImmutable();
    $this->syncOriginal();
  }

  public function getId(): ?int {
    return $this->id;
  }

  public function getTitle(): string {
    return $this->title;
  }

  public function setTitle(string $title): void {
    $this->title = $title;
  }

  public function isCompleted(): bool {
    return $this->completed;
  }

  public function setCompleted(bool $completed): void {
    $this->completed = $completed;
  }

  public function getCreatedAt(): DateTimeImmutable {
    return $this->createdAt;
  }

  public function getUser(): User {
    return $this->user;
  }

  public function setUser(User $user): void {
    $this->user = $user;
  }
}