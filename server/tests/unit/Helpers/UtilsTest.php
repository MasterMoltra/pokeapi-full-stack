<?php

namespace Helpers;

use App\Helpers\Utils;

class UtilsTest extends \Codeception\Test\Unit
{
    /** 
     * List of non Ascii string that have to convert and sanitize
     * @return array
     */
    public function stringsDataProvider(): array
    {
        return [
            ['pokémon', 'pokemon'],
            ['pikachū', 'pikachu'],
            ['Porygon-Z', 'porygon-z'],
            ['Flabébé', 'flabebe'],
            ['Farfetch\'d', 'farfetchd'],
            ['Tapu Bulu', 'tapu-bulu'],
            ['òàùèìü', 'oaueiu'],
            ['https://malware.domaìn.com/test', 'test']
        ];
    }

    /**
     * @test
     * @dataProvider stringsDataProvider
     */
    public function testConvertStringToValidAsciiUrlPath(string $initial, string $expeted): void
    {
        $sanitize = Utils::sanitizePathUrl($initial);
        $this->assertEquals($expeted, $sanitize);
    }
}
