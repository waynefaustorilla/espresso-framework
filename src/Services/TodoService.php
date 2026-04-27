<?php

declare(strict_types=1);

namespace Espresso\Services;

use Doctrine\ORM\EntityManager;
use Espresso\Database\Entities\Todo;
use Espresso\Database\Repository\TodoRepository;
use Espresso\Http\Exception\HttpException;

class TodoService extends AbstractService {
  public function __construct(EntityManager $entityManager, private readonly TodoRepository $todoRepository) {
    parent::__construct($entityManager);
  }

  public function all(): array {
    return $this->todoRepository->findAllOrderedByDate();
  }

  public function findOrFail(int $id): Todo {
    $todo = $this->todoRepository->find($id);

    if (!$todo instanceof Todo) {
      throw new HttpException(404, "Todo not found.");
    }

    return $todo;
  }

  public function create(array $data): Todo {
    $todo = new Todo($data["title"]);

    $this->todoRepository->save($todo);

    return $todo;
  }

  public function update(int $id, array $data): Todo {
    $todo = $this->findOrFail($id);
    $todo->setTitle($data["title"]);

    $this->todoRepository->save($todo);

    return $todo;
  }

  public function toggleComplete(int $id): Todo {
    $todo = $this->findOrFail($id);
    $todo->setCompleted(!$todo->isCompleted());

    $this->todoRepository->save($todo);

    return $todo;
  }

  public function delete(int $id): void {
    $todo = $this->findOrFail($id);

    $this->todoRepository->delete($todo);
  }
}