<?php

namespace App\Helpers;

use Symfony\Component\HttpFoundation\Request;

class Utils
{
    /**
     * @var string
     */
    private const LOG_DIR = __DIR__ . '/../../../var/php';

    public static function logRequestInfo(
        ?Request $request = null,
        ?array $extra = null,
        ?string $name = null
    ): void {
        $fileName = $name ? $name . '_' : (null === $request ? 'logs_' : 'requests_');
        $fileName .= date('j.n.Y');

        $objects = null === $request ?
            [] :
            [
                'content' => $request->getContent(),
                'request' => $request->request,
                'query' => $request->query,
            ];

        if (is_array($extra)) {
            $objects = array_merge($objects, $extra);
        }

        file_put_contents(
            self::LOG_DIR . '/' . $fileName  . '.log',
            "\r\n\r\n ---------------------------------------- \r\n " . date('H:i:s'),
            FILE_APPEND
        );

        foreach ($objects as $key => $obj) {
            file_put_contents(
                self::LOG_DIR . '/' . $fileName  . '.log',
                "\r\n ----- {$key} ----- \r\n" . print_r($obj, true),
                FILE_APPEND
            );
        }
    }

    public static function sanitizePathUrl(string $path): string
    {
        $path = strtolower(
            iconv(
                'UTF-8',
                'ASCII//TRANSLIT//IGNORE',
                parse_url($path, PHP_URL_PATH)
            )
        );
        $path = preg_replace('/[[:space:]]+/', '-', $path);

        return preg_replace('/[^a-zA-Z-]/', '', $path);
    }
}
