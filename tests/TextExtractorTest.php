<?php

namespace bpteam\QuickParserWizard\Tests;

use PHPUnit\Framework\TestCase;
use bpteam\QuickParserWizard\TextExtractor;

class TextExtractorTest extends TestCase
{
    /** @dataProvider dataProviderBetweenTags */
    public function testBetweenTags(string $test, string $tag, string $expected): void
    {
        $extractor = new TextExtractor();

        $this->assertEquals(
            $expected,
            $extractor->betweenTags($test, $tag),
        );
    }

    public function dataProviderBetweenTags(): iterable
    {
        yield [
            '<div>test</div>',
            '<div>',
            'test',
        ];

        yield [
            '<p>test</p><div>I am test <div class="test">Hi<div> you are cool
Перевірка UTF8</div></div>:)</div>',
            '<div class="test">',
            'Hi<div> you are cool
Перевірка UTF8</div>',
        ];

        yield [
            mb_convert_encoding(file_get_contents(__DIR__ . '/fixtures/001-ru-for-parts.html'), 'utf-8', 'cp-1251'),
            '<div class="rst-page-wrap">',
            mb_convert_encoding(file_get_contents(__DIR__ . '/fixtures/001-ru-for-parts-result.html'), 'utf-8', 'cp-1251'),
        ];
    }
}