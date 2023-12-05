<?php

namespace bpteam\QuickParserWizard\Collection;

use ArrayIterator;
use CachingIterator;
use InvalidArgumentException;
use Iterator;
use LogicException;

abstract class AbstractObjectCollection extends CachingIterator implements CollectionInterface
{
    private string $className;

    protected function __construct(string $className, iterable $items)
    {
        $this->className = $className;

        if (is_array($items)) {
            $iterator = new ArrayIterator($items);
        } elseif ($items instanceof Iterator) {
            $iterator = $items;
        } else {
            throw new LogicException(
                'Invalid format of collection elements it should be instance of Traversable or array'
            );
        }

        parent::__construct($iterator, CachingIterator::TOSTRING_USE_KEY | CachingIterator::FULL_CACHE);
    }

    public function __wakeup()
    {
        $this->__construct($this->className, $this->getInnerIterator());
    }

    public function current(): mixed
    {
        $current = parent::current();
        $this->validate($current);
        return $current;
    }

    public function append($element): void
    {
        $this->validate($element);
        $this[] = $element;
    }

    public function jsonSerialize(): array
    {
        $data = [];
        $this->rewind();
        foreach ($this as $item) {
            $data[] = $item;
        }

        return $data;
    }

    protected function validate($item): void
    {
        if (false === ($item instanceof $this->className)) {
            throw new InvalidArgumentException(
                "Collection wait item with class {$this->className} had " . get_class($item)
            );
        }
    }
}