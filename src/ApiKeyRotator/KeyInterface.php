<?php

namespace bpteam\QuickParserWizard\ApiKeyRotator;

interface KeyInterface
{
    public function getCredentials(): mixed;
}