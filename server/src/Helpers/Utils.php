<?php

namespace App\Helpers;

use Symfony\Component\HttpFoundation\Request;

class Utils
{
    /**
     * @var string
     */
    private const LOG_DIR = __DIR__ . '/../../../var/php';

    public static function logRequestInfo(Request $request, ?array $extra = null): void
    {
        $date = date('j.n.Y');
        $objects = [
            'content' => $request->getContent(),
            'request' => $request->request,
            'query' => $request->query,
        ];

        if (is_array($extra)) {
            $objects = array_merge($objects, $extra);
        }

        foreach ($objects as $key => $obj) {
            file_put_contents(
                self::LOG_DIR . '/requests_' . $date  . '.log',
                "\r\n-----------------{$key}---------------\r\n" . print_r($obj, true),
                FILE_APPEND
            );
        }
    }
}
