<?php

namespace App\Controller;

use App\Helpers\Utils;
use App\Template;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractController
{
    public function render(Request $request, array $routeInfo): Response
    {
        $method = strtoupper($request->getMethod());
        if (
            key_exists('allowed_methods', $routeInfo) &&
            !in_array($method, $routeInfo['allowed_methods'])
        ) {
            throw new BadRequestException("Method {$method} not allowed");
        }

        // Get data from a controller Action
        if (false !== strpos($routeInfo['name'], 'Controller:')) {
            $args = null;
            $getpost = 'GET' === $method ? 'query' : 'request';
            $data = json_decode($request->getContent(), true);

            foreach ($routeInfo['args'] as $key) {
                if (is_array($data) && key_exists($key, $data)) {
                    // From content type application/json
                    $args[] = $data[$key];
                } else {
                    // Url Syntax for scalar and array ?key=value&array[key]=value
                    $args[] = $request->{$getpost}->get($key);
                }
            }

            // Log the request
            // Utils::logRequestInfo($request, ['args' => $args]);

            $action = explode(':', $routeInfo['name'])[1];

            return (new BaseController())->{$action}(...$args);
        }

        // Otherwise include a raw file name
        $template = new Template(sprintf(__DIR__ . '/../../templates/%s.php', $routeInfo['name']));

        if (!empty($routeInfo['template_vars'])) {
            foreach (array_keys($routeInfo['template_vars']) as $key) {
                $template->set($key, $routeInfo['template_vars'][$key]);
            }
        }

        return new Response($template->render());
    }
}
