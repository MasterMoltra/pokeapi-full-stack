<?php

namespace App\Controller;

use App\Helpers\Utils;
use App\Pokemon;
use App\Service\SimpleCache;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class BaseController extends AbstractController
{
    /**
     * Generate e Json Response with a pokemon data html block.
     *
     * @param string     $name     Pokemon name to get data
     * @param null|array $metadata Extra useful info about the client device
     */
    public function jsonInfosByPath(string $name, ?array $metadata = null): JsonResponse
    {
        sleep(1); // Simulate request waiting ...

        $path = Utils::sanitizePathUrl($name);
        $channel = $metadata['mode'] ?? null;
        $pokemon = new Pokemon(
            $channel,
            SimpleCache::getInstance('pokeapi')
        );

        switch ($channel) {
            case 'local':
                /** Notice: local require searching in the root json file to retrieve the real path of the searched Pokemon */
                $arrayPokeList = $pokemon->getLocalRootJson();
                $pokeKey = array_search(
                    $path,
                    array_column($arrayPokeList['results'], 'name')
                );

                $jsonUrl = false === $pokeKey ?
                    null :
                    $arrayPokeList['results'][$pokeKey]['url'];

                break;
            case 'api':
                $jsonUrl = $path;

                break;
            default:
                $jsonUrl = null;

                break;
        }

        // Box template output
        $outputData = '';
        if (!$jsonUrl) {
            $outputData .= '<h2 class="error-bg">Sorry, Pok&eacute;mon Not Found</h2>';
        } else {
            // Get pokemon data
            $data = $pokemon->getData($jsonUrl);

            if (!$data) {
                $outputData .= '<h2 class="error-bg">Sorry, Pok&eacute;mon Not Found</h2>';
            } else {
                // Notify if the output is from cached data
                $outputData .= $data['__from__'] ?
                    '<div class="block-highlight">' . $data['__from__'] . '</div>' :
                    '';
                $outputData .= !empty($data['name']) ?
                    '<h2>' . ucfirst($data['name']) . '</h2>' : '';

                $imageUrl =
                    !empty($metadata['device']) &&
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
     * Generate a full Html optimized Seo Static page response.
     *
     * @param string $name Pokemon name to get data
     */
    public function htmlStaticSeoByPath(string $name): Response
    {
        $pokemon = new Pokemon(
            'local', // NOTICE: Force LOCAL mode
            SimpleCache::getInstance('pokeapi')
        );
        $arrayPokeList = $pokemon->getLocalRootJson();
        $path = Utils::sanitizePathUrl($name);
        $pokeKey = array_search(
            $path,
            array_column($arrayPokeList['results'], 'name')
        );

        if (false === $pokeKey) {
            // TODO: redirect to home page
            return new Response('Page Not Found !!!');
        }

        $jsonUrl = $arrayPokeList['results'][$pokeKey]['url'];
        $data = $pokemon->getData($jsonUrl);

        if (!$data) {
            // TODO: redirect to home page
            return new Response('Page Not Found !!!');
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
     */
    protected function pokemonRenderSeoBox(array $data): string
    {
        $outputData = '<div class="box">';
        // Notify if the output is from cached data
        $outputData .= $data['__from__'] ?
            '<div class="block-highlight">' . $data['__from__'] . '</div>' :
            '';
        $outputData .= '<h1>' . ucfirst($data['name']) . '</h1>';

        $imageUrl = $data['sprites']['front_defaultz'] ??
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
}
