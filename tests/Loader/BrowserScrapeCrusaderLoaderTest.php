<?php

namespace bpteam\QuickParserWizard\Tests\Loader;

use bpteam\QuickParserWizard\Loader\BrowserScrapeCrusaderLoader;
use PHPUnit\Framework\TestCase;

class BrowserScrapeCrusaderLoaderTest extends TestCase
{
    public function testLoad()
    {
        $browserScrapeCrusaderLoader = new BrowserScrapeCrusaderLoader(
            getenv('RAPID_API_KEY'),
        );

        $content = $browserScrapeCrusaderLoader->load('https://example.com');
        $this->assertStringContainsString('<h1>Example Domain</h1>', $content->body);
    }
}