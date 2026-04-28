<?php

declare(strict_types=1);

namespace Espresso\Services;

use Espresso\Database\Entities\Todo;
use Espresso\Database\Entities\User;
use Espresso\Database\Repository\TodoRepositoryInterface;
use Espresso\Http\Exception\HttpException;

class TodoService {
  public function __construct(
    private readonly TodoRepositoryInterface $todoRepository,
    private readonly TodoTransformer $transformer,
  ) {}

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

  public function create(array $data, User $user): Todo {
    $todo = $this->transformer->fromArray($data, $user);

    $this->todoRepository->save($todo);

    return $todo;
  }

  public function update(int $id, array $data): Todo {
    $todo = $this->findOrFail($id);
    $this->transformer->applyUpdate($todo, $data);

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