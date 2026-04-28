<?php

declare(strict_types=1);

namespace Espresso\Http\Controllers;

use Espresso\Auth\AuthManager;
use Espresso\Database\Entities\User;
use Espresso\Http\Controller\AbstractController;
use Espresso\Http\Exception\HttpException;
use Espresso\Http\Factory\FormRequestFactory;
use Espresso\Http\Request;
use Espresso\Http\Requests\StoreTodoRequest;
use Espresso\Http\Requests\UpdateTodoRequest;
use Espresso\Http\Response\ResponseFactory;
use Espresso\Services\TodoService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class TodoController extends AbstractController {
  public function __construct(
    ResponseFactory $responseFactory,
    FormRequestFactory $formRequestFactory,
    private readonly TodoService $todoService,
    private readonly AuthManager $authManager,
  ) {
    parent::__construct($responseFactory, $formRequestFactory);
  }

  public function index(ServerRequestInterface $request): ResponseInterface {
    $todos = $this->todoService->all();

    return $this->view("todos/index.latte", ["todos" => $todos]);
  }

  public function create(ServerRequestInterface $request): ResponseInterface {
    return $this->view("todos/create.latte");
  }

  public function store(ServerRequestInterface $request): ResponseInterface {
    $data = $this->formRequest(StoreTodoRequest::class, $request)->validated();
    $user = $this->resolveAuthenticatedUser();

    $this->todoService->create($data, $user);

    return $this->redirect("/todos");
  }

  public function edit(ServerRequestInterface $request): ResponseInterface {
    $id = (int) $request->getAttribute("id");
    $todo = $this->todoService->findOrFail($id);

    return $this->view("todos/edit.latte", ["todo" => $todo]);
  }

  public function update(ServerRequestInterface $request): ResponseInterface {
    $id = (int) $request->getAttribute("id");
    $data = $this->formRequest(UpdateTodoRequest::class, $request)->validated();

    $this->todoService->update($id, $data);

    return $this->redirect("/todos");
  }

  public function toggle(ServerRequestInterface $request): ResponseInterface {
    $id = (int) $request->getAttribute("id");

    $this->todoService->toggleComplete($id);

    return $this->redirect("/todos");
  }

  public function destroy(ServerRequestInterface $request): ResponseInterface {
    $id = (int) $request->getAttribute("id");

    $this->todoService->delete($id);

    return $this->redirect("/todos");
  }

  private function resolveAuthenticatedUser(): User {
    $user = $this->authManager->guard()->user();

    if (!$user instanceof User) {
      throw new HttpException(401, "Unauthenticated.");
    }

    return $user;
  }
}

    return $this->redirect("/todos");
  }

  public function destroy(ServerRequestInterface $request): ResponseInterface {
    $id = (int) $request->getAttribute("id");

    $this->todoService->delete($id);

    return $this->redirect("/todos");
  }
}