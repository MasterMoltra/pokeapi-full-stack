<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;

class BaseController extends AbstractController
{
    public function infosByPath(string $path, ?array $metadata = null): JsonResponse
    {
        $path = filter_var($path, FILTER_SANITIZE_URL);
        sleep(3);
        $content = "
        <div class=\"box\">
            <h3>Response from my PHP conteoller!</h3>
            <p>{$path}</p>
        </div>";

        return new JsonResponse([
            'metadata' => $metadata,
            'content' => $content,
        ]);
    }
}
