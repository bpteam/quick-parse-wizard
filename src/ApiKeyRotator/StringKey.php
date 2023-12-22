<?php

namespace bpteam\QuickParserWizard\ApiKeyRotator;

class StringKey implements KeyInterface
{
    public function __construct(
        private readonly string $key,
    ) {}

    public function getCredentials(): string
    {
        return $this->key;
    }
}