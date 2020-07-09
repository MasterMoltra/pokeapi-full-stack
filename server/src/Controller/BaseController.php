<?php

namespace App\Controller;

use App\Helpers\Utils;
use App\Pokemon;
use App\Service\SimpleCache;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
                $pokeKey = $arrayPokeList ?
                    array_search(
                        $path,
                        array_column($arrayPokeList['results'], 'name')
                    ) :
                    false;

                $jsonUrl = is_int($pokeKey) ? $arrayPokeList['results'][$pokeKey]['url'] : null;

                break;
            case 'api':
                $jsonUrl = $path;

                break;
            default:
                $jsonUrl = null;

                break;
        }

        // Box template output
        $output = '';
        if (!$jsonUrl) {
            $output .= '<h2 class="error-bg">Sorry, Pok&eacute;mon Not Found</h2>';
        } else {
            // Get pokemon data
            $data = $pokemon->getData($jsonUrl);

            if (!$data) {
                $output .= '<h2 class="error-bg">Sorry, Pok&eacute;mon Not Found</h2>';
            } else {
                $output .= '<h2>' . ucfirst($data['name']) . '</h2>';
                $output .= $this->pokemonRenderBoxData($data, $metadata);
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
            {$output}
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
        $pokeKey = $arrayPokeList ?
            array_search(
                $path,
                array_column($arrayPokeList['results'], 'name')
            ) :
            false;

        if (false === $pokeKey) {
            return new RedirectResponse('/');
        }

        $jsonUrl = $arrayPokeList['results'][$pokeKey]['url'];
        $data = $pokemon->getData($jsonUrl);

        if (!$data) {
            return new RedirectResponse('/');
        }

        // Box template output
        $output = '<!DOCTYPE html><html lang="en">';
        $output .= file_get_contents(__DIR__ . '/../../templates/partials/head.php');
        $output .= '<body>';
        $output .= '<div class="box">';
        $output .= '<h1>' . ucfirst($data['name']) . ' (Seo static render)</h1>';
        $output .= $this->pokemonRenderBoxData($data);
        $output .= '</div></body></html>';

        return new Response($output);
    }

    /**
     * Generate a pokemon html block content.
     */
    protected function pokemonRenderBoxData(array $data, ?array $metadata = null): string
    {
        $output = '';
        // Print image (by device if detected)
        $imageUrl = !empty($metadata['device']) &&
            'mobile' == $metadata['device'] ?
            $data['sprites']['back_default'] ?? $data['sprites']['front_shiny'] :
            $data['sprites']['front_default'];
        $output .= $imageUrl ?
            "<div><img src=\"{$imageUrl}\" width=\"150\" /></div>"
            : '';
        // Print some ramndom infos
        $output .= "ID.{$data['id']} - Weight {$data['weight']} - Height {$data['height']}";
        // Print indicies block
        $output .= '<p><strong>GAMES INDICIES:</strong><br>';
        $gamesIndicies = '';
        foreach ($data['game_indices'] as $key => $names) {
            foreach ($names as $name => $value) {
                if ('version' == $name) {
                    $gamesIndicies .= "{$value['name']} | ";
                }
            }
        }
        $output .= $gamesIndicies ? rtrim($gamesIndicies, ' | ') : 'None!';
        $output .= '</p>';
        // Notify if output is from cached data
        $output .= $data['__from__'] ?
            '<div class="block-highlight">' . $data['__from__'] . '</div>' :
            '';

        return $output;
    }
}
