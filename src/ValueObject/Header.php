<?php

namespace bpteam\QuickParserWizard\ValueObject;

class Header
{
    public function __construct(
        public readonly string $name,
        public readonly string $value,
    ){}
}