<?php

namespace bpteam\QuickParserWizard\ValueObject;

use bpteam\QuickParserWizard\Collection\AbstractObjectCollection;

class HeaderCollection extends AbstractObjectCollection
{
    public function __construct(iterable $items){
        parent::__construct(Header::class, $items);
    }
}