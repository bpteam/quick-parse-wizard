<?php

namespace bpteam\QuickParserWizard\Loader;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleRetry\GuzzleRetryMiddleware;

class GuzzleWithRetryLoader extends GuzzleLoader
{
    private function __construct(
        Client $client,
    ) {
        parent::__construct($client);
    }

    public static function create(
        array $defaultGuzzleOptions = [],
        array $defaultRetryOptions = [
            'retry_on_status' => ['503', '502', '429'],
            'default_retry_multiplier' => 0,
            'retry_on_timeout' => true,
        ],
    ): self {
        $stack = $defaultGuzzleOptions['handler'] ?? HandlerStack::create();
        $stack->push(GuzzleRetryMiddleware::factory($defaultRetryOptions));
        $defaultGuzzleOptions['handler'] = $stack;
        $client = new Client($defaultGuzzleOptions);
        return new self($client);
    }
}