<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;

abstract class AbstractController
{
    public function render(string $filename): Response
    {
        ob_start();
        include sprintf(__DIR__ . '/../../templates/%s.php', $filename);

        return new Response(ob_get_clean());
    }
}
