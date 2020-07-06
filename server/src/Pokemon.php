<?php

namespace App;

use App\Helpers\Utils;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class Pokemon
{
    /**
     * @var string Base url of the public Api
     */
    private const API_URL = 'https://pokeapi.co/api/v2/pokemon';

    /**
     * @var string Base path of the local Json directories
     */
    private const LOCAL_DIR = __DIR__ . '/../..';

    /**
     * @var string The main local pokemon Json (with the full list of pokemon)
     */
    private const LOCAL_ROOT_JSON = self::LOCAL_DIR . '/api/v2/pokemon/index.json';

    /** @var null|array */
    protected $data;

    /** @var string */
    protected $channel;

    /**
     * Init Pokemon class.
     *
     * @param null|string $channel A channel to retrieve the data (e.g. local | api)
     */
    public function __construct(?string $channel)
    {
        $this->channel = $channel ?? 'local';
    }

    /**
     * Return json data.
     *
     * @param string $path Full path to get data
     */
    public function getData(string $path): ?array
    {
        $this->init($path); // Load data only on demand (proxy pattern)

        return $this->data;
    }

    public function getLocalRootJson(bool $onlyCheck = false)
    {
        if (!is_readable(self::LOCAL_ROOT_JSON)) {
            return $onlyCheck ? false : null;
            // throw new  FileNotFoundException('Local files not founds');
        }

        $json = file_get_contents(self::LOCAL_ROOT_JSON);

        return json_decode($json, true);
    }

    /**
     * Require the data only when is needed.
     *
     * @param string $path Full path to get data
     */
    protected function init(string $path): void
    {
        // TODO: replace with adapters class
        switch ($this->channel) {
            case 'local':
                $this->data = $this->generateFromLocal($path);

                break;
            case 'api':
                $this->data = $this->generateFromApi($path);

                break;
            default:
                throw new BadRequestException("Channel {$this->channel} is not allowed");

                break;
        }

        // Utils::logRequestInfo(null, ['args' => $this->data['sprites']]);
    }

    /**
     * Retrieve json data with a full path.
     *
     * @param string $path Full path to get data
     */
    protected function generateFromLocal(string $path): ?array
    {
        // Utils::logRequestInfo(null, ['LOCAL' => self::LOCAL_DIR . $path]);
        $fullJsonPath = self::LOCAL_DIR . $path  . '/index.json';
        if (!is_readable($fullJsonPath)) {
            return null;
        }

        $json = file_get_contents($fullJsonPath);

        return json_decode($json, true);
    }

    /**
     * Retrieve json data with a public Api request.
     *
     * @param string $path Full path to get data
     */
    protected function generateFromApi(string $path): ?array
    {
        // Utils::logRequestInfo(null, ['API' => self::API_URL . '/' . $path]);
        $json = $this->getExternalRequest(self::API_URL . '/' . $path);

        return  json_decode($json, true);
    }

    private function getExternalRequest(string $url)
    {
        $ch = curl_init();
        $timeout = 5;

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

        $data = curl_exec($ch);

        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if (200 != $http_code) {
            throw new \RuntimeException($url, $data);
        }

        return $data;
    }
}
