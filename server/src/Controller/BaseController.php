<?php

namespace App\Controller;

use App\Helpers\Utils;
use Symfony\Component\HttpFoundation\JsonResponse;

class BaseController extends AbstractController
{
    private const API_BASE_DIR = __DIR__ . '/../../..';

    public function infosByPath(string $name, ?array $metadata = null): JsonResponse
    {
        sleep(1); // Simulate request waiting ...

        $arrayPokeList = $this->pokemonJsonToarray();
        $path = Utils::sanitizePathUrl($name);
        $pokeKey = array_search($path, array_column($arrayPokeList['results'], 'name'));

        // Box template otuput
        $outputData = '';
        if (false === $pokeKey) {
            $outputData .= '<h2 class="error-bg">Sorry, Pok&eacute;mon Not Found</h2>';
        } else {
            $jsonUrl = $arrayPokeList['results'][$pokeKey]['url'];
            $data = $this->pokemonJsonToarray(self::API_BASE_DIR . $jsonUrl);
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

    private function pokemonJsonToarray(?string $path = null): array
    {
        $jsonPokemonList = file_get_contents(
            ($path ?? self::API_BASE_DIR . '/api/v2/pokemon/') . 'index.json'
        );

        return json_decode($jsonPokemonList, true);
    }
}
