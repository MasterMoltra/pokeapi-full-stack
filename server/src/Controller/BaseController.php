<?php

namespace App\Controller;

use App\Helpers\Utils;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class BaseController extends AbstractController
{
    /** @var string A local base directory path with json data */
    private const API_BASE_DIR = __DIR__ . '/../../..';

    /**
     * Generate e Json Response with a pokemon data html block.
     *
     * @param string $name Pokemon name to get data
     * @param array|null $metadata Extra useful info about the client device
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function jsonInfosByPath(string $name, ?array $metadata = null): JsonResponse
    {
        sleep(1); // Simulate request waiting ...

        $arrayPokeList = $this->getPokemonDataArray();
        $path = Utils::sanitizePathUrl($name);
        $pokeKey = array_search(
            $path,
            array_column($arrayPokeList['results'], 'name')
        );

        // Box template output
        $outputData = '';
        if (false === $pokeKey) {
            $outputData .= '<h2 class="error-bg">Sorry, Pok&eacute;mon Not Found</h2>';
        } else {
            $jsonUrl = $arrayPokeList['results'][$pokeKey]['url'];
            $data = $this->getPokemonDataArray(self::API_BASE_DIR . $jsonUrl);
            $outputData .= $data && key_exists('name', $data) ?
                '<h2>' . ucfirst($data['name']) . '</h2>' :
                '<h2 class="error-bg">Sorry, Pok&eacute;mon Not Found</h2>';
            if ($data) {
                $imageUrl =
                    array_key_exists('device', $metadata) &&
                    'mobile' == $metadata['device'] ?
                    $data['sprites']['back_default'] ?? $data['sprites']['front_shiny'] :
                    $data['sprites']['front_default'];
                $outputData .= $imageUrl ?
                    "<div><img src=\"{$imageUrl}\" width=\"150\" /></div>"
                    : '';

                $outputData .= "ID.{$data['id']} - Weight {$data['weight']} - Height {$data['height']}";

                $outputData .= '<p><strong>GAMES INDICIES:</strong><br>';
                $gamesIndicies = '';
                foreach ($data['game_indices'] as $key => $names) {
                    foreach ($names as $name => $value) {
                        if ('version' == $name) {
                            $gamesIndicies .= "{$value['name']} | ";
                        }
                    }
                }
                $outputData .= $gamesIndicies ? rtrim($gamesIndicies, ' | ') : 'None!';
                $outputData .= '</p>';
            }
        }

        // Device client infos output
        $outputMetadata = '<p style="text-align:center;">';
        foreach ($metadata as $key => $value) {
            if ('device' === $key || 'display' === $key) {
                $sanitizedData = filter_var(
                    $value,
                    FILTER_SANITIZE_STRING,
                    FILTER_FLAG_STRIP_LOW
                );
                $outputMetadata .= "<strong>{$key}</strong> => {$sanitizedData} | ";
            }
        }
        $outputMetadata = rtrim($outputMetadata, ' | ') . '</p>';

        $content = "
        <div class=\"box\">
            {$outputData}
        </div>
        <small>{$outputMetadata}</small>
        ";

        return new JsonResponse([
            'path' => $path,
            'content' => $content,
        ]);
    }

    /**
     * Generate a full Html optimized Seo Static page response
     *
     * @param string $name Pokemon name to get data
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function htmlStaticSeoByPath(string $name): Response
    {
        $arrayPokeList = $this->getPokemonDataArray();
        $path = Utils::sanitizePathUrl($name);
        $pokeKey = array_search(
            $path,
            array_column($arrayPokeList['results'], 'name')
        );

        if (false === $pokeKey) {
            // TODO: redirect to home page
            return new Response("Page Not Found !!!");
        }

        $jsonUrl = $arrayPokeList['results'][$pokeKey]['url'];
        $data = $this->getPokemonDataArray(self::API_BASE_DIR . $jsonUrl);

        if (!$data) {
            // TODO: redirect to home page
            return new Response("Page Not Found !!!");
        }

        // Box template output
        $content = '<!DOCTYPE html><html lang="en">';
        $content .= file_get_contents(__DIR__ . '/../../templates/partials/head.php');
        $content .= '<body>';
        $content .= $this->pokemonRenderSeoBox($data);
        $content .= '</body></html>';

        return new Response($content);
    }

    /**
     * Generate a single Html pokemon block box.
     *
     * @param array $data
     *
     * @return string
     */
    protected function pokemonRenderSeoBox(array $data): string
    {
        $outputData = '<div class="box">';
        $outputData .= '<h2>' . ucfirst($data['name']) . '</h2>';

        $imageUrl =  $data['sprites']['front_defaultz'] ??
            $data['sprites']['front_shiny'] ??
            $data['sprites']['back_default'];
        $outputData .= $imageUrl ?
            "<div><img src=\"{$imageUrl}\" width=\"150\" /></div>"
            : '';

        $outputData .= "ID.{$data['id']} - Weight {$data['weight']} - Height {$data['height']}";

        $outputData .= '<p><strong>GAMES INDICIES:</strong><br>';
        $gamesIndicies = '';
        foreach ($data['game_indices'] as $key => $names) {
            foreach ($names as $name => $value) {
                if ('version' == $name) {
                    $gamesIndicies .= "{$value['name']} | ";
                }
            }
        }
        $outputData .= $gamesIndicies ? rtrim($gamesIndicies, ' | ') : 'None!';
        $outputData .= '</p>';
        $outputData .= '</div>';

        return $outputData;
    }

    /**
     * Retrieve json data by a full path
     *
     * @param string|null $path Full path to get data
     * @param string $channel A specific channel to retrieve data (e.g. local | api)
     *
     * @return array
     */
    protected function getPokemonDataArray(?string $path = null, string $channel = 'local'): ?array
    {
        $jsonPokemonList = file_get_contents(
            ($path ?? self::API_BASE_DIR . '/api/v2/pokemon/') . 'index.json'
        );

        return json_decode($jsonPokemonList, true);
    }
}
