<?php

namespace bpteam\QuickParserWizard\Tests\Loader;

use bpteam\QuickParserWizard\Exception\ResponseInvalidException;
use bpteam\QuickParserWizard\Loader\GuzzleLoader;
use bpteam\QuickParserWizard\Loader\ResponseValidatorByRegexpLoader;
use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class ResponseValidatorByRegexpLoaderTest extends TestCase
{
    public function testLoad()
    {
        $loader = new ResponseValidatorByRegexpLoader(
            new GuzzleLoader(
                new Client(),
            ),
            '/<h1>Example Domain<\/h1>/',
        );

        $content = $loader->load('https://example.com');
        $this->assertStringContainsString('<h1>Example Domain</h1>', $content->body);
    }


    public function testFailLoad()
    {
        $loader = new ResponseValidatorByRegexpLoader(
            new GuzzleLoader(
                new Client(),
            ),
            '/asdf0123456789/',
        );
        $this->expectException(ResponseInvalidException::class);
        $loader->load('https://example.com');
    }
}