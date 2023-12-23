<?php

namespace bpteam\QuickParserWizard\ApiKeyRotator;

class Key implements KeyInterface
{
    public function __construct(
        private readonly mixed $key,
    ) {}

    public function getCredentials(): mixed
    {
        return $this->key;
    }
}