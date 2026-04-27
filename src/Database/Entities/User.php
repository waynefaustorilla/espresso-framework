<?php

declare(strict_types=1);

namespace Espresso\Database\Entities;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Espresso\Auth\Contracts\Authenticatable;
use Espresso\Database\Concerns\HasMagicProperties;
use JsonSerializable;

#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
#[ORM\Table(name: "users")]
class User implements Authenticatable, JsonSerializable {
  use HasMagicProperties;

  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column(type: "integer")]
  private ?int $id = null;

  #[ORM\Column(type: "string", length: 255)]
  private string $name;

  #[ORM\Column(type: "string", length: 255, unique: true)]
  private string $email;

  #[ORM\Column(type: "string", length: 255)]
  private string $password;

  #[ORM\Column(type: "datetime_immutable")]
  private DateTimeImmutable $createdAt;

  #[ORM\OneToMany(mappedBy: "user", targetEntity: Todo::class, cascade: ["persist", "remove"])]
  private Collection $todos;

  public function __construct(string $name, string $email, string $password) {
    $this->name = $name;
    $this->email = $email;
    $this->password = password_hash($password, PASSWORD_BCRYPT);
    $this->createdAt = new DateTimeImmutable();
    $this->todos = new ArrayCollection();
    $this->syncOriginal();
  }

  public function getId(): int|string {
    return $this->id;
  }

  public function getName(): string {
    return $this->name;
  }

  public function setName(string $name): void {
    $this->name = $name;
  }

  public function getEmail(): string {
    return $this->email;
  }

  public function setEmail(string $email): void {
    $this->email = $email;
  }

  public function getPassword(): string {
    return $this->password;
  }

  public function setPassword(string $password): void {
    $this->password = password_hash($password, PASSWORD_BCRYPT);
  }

  public function getCreatedAt(): DateTimeImmutable {
    return $this->createdAt;
  }

  public function getTodos(): Collection {
    return $this->todos;
  }
}