<?php

namespace bpteam\QuickParserWizard\ValueObject;

use bpteam\QuickParserWizard\Enum\HttpMethod;

class NextPageLoaderRequest
{
    public function __construct(
        public readonly string $url,
        public readonly HttpMethod $httpMethod,
        public readonly ?string $body = null,
        public readonly ?HeaderCollection $headerCollection = null,
    ){}
}