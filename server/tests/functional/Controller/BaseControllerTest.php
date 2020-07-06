<?php

namespace Test\Controller;

use App\Helpers\Utils;
use Symfony\Component\HttpFoundation\Request;

/**
 * @internal
 * @coversNothing
 */
class BaseControllerTest extends \Codeception\Test\Unit
{
    /**
     * @var \Test\FunctionalTester
     */
    protected $tester;

    /**
     * @var \Codeception\Module\PhpBrowser
     */
    protected $client;

    /**
     * List of routes to test.
     */
    public function routesDataProvider(): array
    {
        return [
            ['/',               'GET',  [],                         200],
            ['/dash',           'GET',  [],                         200],
            ['wrongroute',      'GET',  [],                         404],
            ['/php/pokeinfos',  'GET',  ['name=bulbasaur']],
            ['/php/pokeinfos',  'POST', ['name' => 'bulbasaur']],
            ['/api',            'GET',  ['name=Flabébé'],         200],
            ['/api',            'POST', ['name' => 'bulbasaur'],    500],
        ];
    }

    /**
     * @test
     * @dataProvider routesDataProvider
     */
    public function testControllerRenderResponseStatusCode(
        string $uri,
        string $method,
        array $params,
        ?int $expected = null
    ): void {
        if (null === $expected) {
            $expected = in_array(
                $method,
                $this->tester->_routeGetAllowedMethods($uri),
                true
            ) ? 200 : 500;
        }

        if ('GET' === $method) {
            $this->client->_request(
                $method,
                $uri . '?' . implode($params)
            );
        } else {
            $this->client->_request(
                $method,
                $uri,
                $params
            );
        }

        $this->assertEquals($expected, $this->client->_getResponseStatusCode());
        // Utils::logRequestInfo(new Request(), ['args' => $this->client->_getResponseStatusCode()]);
    }

    /**
     * @test
     */
    public function testControllerRenderValidJsonWithConvertedAsciiPath(): void
    {
        $this->client->_request('POST', '/php/pokeinfos', ['name' => 'Tapu Koko']);
        $this->assertEquals(200, $this->client->_getResponseStatusCode());
        $json = json_decode($this->client->_getResponseContent());
        $this->assertEquals('tapu-koko', $json->path, 'Test if path was correctly converted in ascii format.');
    }

    protected function _before()
    {
        /** @var \Codeception\Module\PhpBrowser */
        $client = $this->getModule('PhpBrowser');
        $this->client = $client;
    }
}
