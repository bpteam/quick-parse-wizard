<?php

namespace bpteam\QuickParserWizard\ApiKeyRotator;

class ApiKeyArrayProvider implements ApiKeyProviderInterface
{
    private array $storage = [];

    public function __construct(
        private readonly float $blockWaitTime = 1.0,
    ) {}

    public function find(int $price, bool $blocking = false): ?KeyInterface
    {
        while (true) {
            $now = new \DateTimeImmutable();
            foreach ($this->storage as $keyName => $item) {
                if ($item['next_flush'] < $now) {
                    $this->storage[$keyName]['next_flush'] = $now->add($item['time_window']);
                    $this->storage[$keyName]['limit'] = $item['init_limit'];
                }
                if ($item['limit'] && $item['limit'] >= $price) {
                    $item['limit'] -= $price;

                    return $item['key'];
                }
            }

            if ($blocking) {
                usleep($this->blockWaitTime * 1000000);
            } else {
                break;
            }
        }

        return null;
    }

    public function add(string $keyName, KeyInterface $key, int $limit, \DateInterval $timeWindow = new \DateInterval('PT1M')): void
    {
        $this->storage[$keyName] = [
            'limit' => $limit,
            'key' => $key,
            'init_limit' => $limit,
            'time_window' => $timeWindow,
            'next_flush' => (new \DateTimeImmutable())->add($timeWindow),
        ];
    }

    public function remove(string $keyName): void
    {
        unset($this->storage[$keyName]);
    }
}