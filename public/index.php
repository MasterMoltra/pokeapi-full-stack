<?php

use App\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

require_once __DIR__ . '/../vendor/autoload.php';

$routes = require_once __DIR__ . '/../server/src/routing.php';

$request = Request::createFromGlobals();

$path = rtrim(strtolower($request->getPathInfo()), '/');
$path = $path ?: '/'; // manage path with and without /

if (!isset($routes[$path])) {
    $response = new Response('Pagina non trovata', 404);
} else {;
    try {
        $response = (new BaseController)->render($routes[$path]);
    } catch (\Throwable $exception) {
        $response = new Response('Errore -> ' . $exception->getMessage(), 500);
    }
}

$response->send();
