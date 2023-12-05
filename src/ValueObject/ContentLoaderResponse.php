<?php

namespace bpteam\QuickParserWizard\ValueObject;

class ContentLoaderResponse
{
    public function __construct(
        public readonly int $code,
        public readonly string $body,
        public readonly HeaderCollection $headerCollection,
    ){}
}