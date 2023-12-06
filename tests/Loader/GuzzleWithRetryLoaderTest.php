<?php

namespace bpteam\QuickParserWizard\Tests\Loader;

use bpteam\QuickParserWizard\Loader\GuzzleWithRetryLoader;
use PHPUnit\Framework\TestCase;

class GuzzleWithRetryLoaderTest extends TestCase
{
    public function testLoad()
    {
        $loader = GuzzleWithRetryLoader::create();

        $content = $loader->load('https://example.com');
        $this->assertStringContainsString('<h1>Example Domain</h1>', $content->body);
    }

    public function testRetryLoad()
    {
        $i = 0;
        $loader = GuzzleWithRetryLoader::create([], [
            'retry_on_status' => ['200',],
            'default_retry_multiplier' => 0,
            'retry_on_timeout' => true,
            'max_retry_attempts' => 3,
            'on_retry_callback' => function() use (&$i) {++$i;},
        ]);

        $content = $loader->load('https://example.com');
        $this->assertEquals(3, $i);
        $this->assertStringContainsString('<h1>Example Domain</h1>', $content->body);
    }
}