<?php

namespace bpteam\QuickParserWizard\Tests;

use PHPUnit\Framework\TestCase;
use bpteam\QuickParserWizard\RegExpGenerator;

class RegExpGeneratorTest extends TestCase
{
    /**
     * @dataProvider regexpHTMLDataProvider
     */
    public function testRegExpByHtml(string $text, string $result, array $checks = []): void
    {
        $generator = new RegExpGenerator();
        $regexp = $generator->regexpByHtml($text);
        $this->assertEquals(
            $result,
            $regexp
        );

        foreach ($checks as $check) {
            $this->assertTrue(
                0 < preg_match('~' . $regexp . '~', $check)
            );
        }
    }

    /**
     * @dataProvider regexpCSSSelectorDataProvider
     */
    public function testRegexpByCssSelector(string $text, string $result, array $checks = []): void
    {
        $generator = new RegExpGenerator();
        $generator = new RegExpGenerator();
        $regexp = $generator->regexpByCssSelector($text);
        $this->assertEquals(
            $result,
            $regexp
        );

        foreach ($checks as $check) {
            $this->assertTrue(
                0 < preg_match('~' . $regexp . '~', $check)
            );
        }
    }

    public function regexpHTMLDataProvider(): iterable
    {
        yield [
            '<div class="container">',
            '<div[^>]*\s*[^>]*class\s*=\s*["\']?[^"\']*container[^"\']*["\']?[^>]*[^>]*>',
            [
                '<div class="container">',
                '<div class="test container" alt=\'asdfasdf\'>',
                '<div class="test container asdf" alt=\'asdfasdf\'>',
                '<div class=" container asdf" alt=\'asdfasdf\'>',
                '<div alt=\'asdfasdf\' class="container asdf" >',
                'asdf dasf a<div alt="asdfasdf" class="container" > <asdfsadF>asdf a</asdfsadF>',
            ]
        ];
    }

    public function regexpCSSSelectorDataProvider(): iterable
    {
        yield [
            'div.container',
            '<div[^>]*\s*[^>]*class\s*=\s*["\']?[^"\']*container[^"\']*["\']?[^>]*[^>]*>',
            [
                '<div class="container">',
                '<div class="test container" alt=\'asdfasdf\'>',
                '<div class="test container asdf" alt=\'asdfasdf\'>',
                '<div class=" container asdf" alt=\'asdfasdf\'>',
                '<div alt=\'asdfasdf\' class="container asdf" >',
                'asdf dasf a<div alt="asdfasdf" class="container" > <asdfsadF>asdf a</asdfsadF>',
            ]
        ];
    }
}
