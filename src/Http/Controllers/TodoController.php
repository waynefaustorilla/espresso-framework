<?php

declare(strict_types=1);

namespace Espresso\Http\Controllers;

use Espresso\Http\Controller\AbstractController;
use Espresso\Http\Requests\StoreTodoRequest;
use Espresso\Http\Requests\UpdateTodoRequest;
use Espresso\Services\TodoService;
use Espresso\Validation\Validator;
use Latte\Engine;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class TodoController extends AbstractController {
  public function __construct(Engine $latte, Validator $validator, private readonly TodoService $todoService) {
    parent::__construct($latte, $validator);
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

    $this->todoService->create($data);

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
}