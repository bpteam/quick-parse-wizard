<?php

namespace bpteam\QuickParserWizard\ApiKeyRotator;

interface ApiKeyProviderInterface
{
    public function find(int $price, bool $blocking = false): ?KeyInterface;
    public function add(string $keyName, KeyInterface $key, int $limit, \DateInterval $timeWindow = new \DateInterval('PT1M')): void;
    public function remove(string $keyName): void;
}