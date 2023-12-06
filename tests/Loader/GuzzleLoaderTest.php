<?php

namespace bpteam\QuickParserWizard\Tests\Loader;

use bpteam\QuickParserWizard\Loader\GuzzleLoader;
use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class GuzzleLoaderTest extends TestCase
{
    public function testLoad()
    {
        $loader = new GuzzleLoader(
            new Client(),
        );

        $content = $loader->load('https://example.com');
        $this->assertStringContainsString('<h1>Example Domain</h1>', $content->body);
    }
}