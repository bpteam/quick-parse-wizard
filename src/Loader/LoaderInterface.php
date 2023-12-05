<?php

namespace bpteam\QuickParserWizard\Loader;

use bpteam\QuickParserWizard\Enum\HttpMethod;
use bpteam\QuickParserWizard\ValueObject\ContentLoaderResponse;
use bpteam\QuickParserWizard\ValueObject\HeaderCollection;

interface LoaderInterface
{
    public function load(string $url, HttpMethod $method = HttpMethod::GET, string $body = null, HeaderCollection $headerCollection = null): ContentLoaderResponse;
}