<?php

namespace App\Controller;

use App\Helpers\Utils;
use Symfony\Component\HttpFoundation\JsonResponse;

class BaseController extends AbstractController
{
    public function infosByPath(string $path, ?array $metadata = null): JsonResponse
    {
        $path = Utils::sanitizePathUrl($path);
        $metadata = filter_var_array($metadata, FILTER_SANITIZE_STRING);
        // sleep(3);

        $content = "
        <div class=\"box\">
            <h3>Response from my PHP conteoller!</h3>
            <p>{$path}</p>
        </div>";

        return new JsonResponse([
            'content' => $content,
        ]);
    }
}
