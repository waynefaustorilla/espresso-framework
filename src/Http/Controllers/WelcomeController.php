<?php

declare(strict_types=1);

namespace Espresso\Http\Controllers;

use Espresso\Http\Attribute\Route;
use Espresso\Http\Controller\AbstractController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class WelcomeController extends AbstractController {
  #[Route("GET", "/")]
  public function index(ServerRequestInterface $request): ResponseInterface {
    return $this->view("welcome.latte");
  }
}
