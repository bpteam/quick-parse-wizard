<?php

namespace bpteam\QuickParserWizard\Loader;

use bpteam\QuickParserWizard\Enum\HttpMethod;
use bpteam\QuickParserWizard\Exception\ResponseInvalidException;
use bpteam\QuickParserWizard\ValueObject\ContentLoaderResponse;
use bpteam\QuickParserWizard\ValueObject\HeaderCollection;

class ResponseValidatorByRegexpLoader implements LoaderInterface
{
    public function __construct(
        private readonly LoaderInterface $loader,
        private readonly string $validateRegexp,
        private readonly int $retryQuantity = 5,
    ) {}

    public function load(string $url, HttpMethod $method = HttpMethod::GET, string $body = null, HeaderCollection $headerCollection = null): ContentLoaderResponse
    {
        for ($i = 0; $i < $this->retryQuantity; $i++) {
            $response = $this->loader->load($url, $method, $body, $headerCollection);
            if (preg_match($this->validateRegexp, $response->body)) {
                return $response;
            }
        }

        throw new ResponseInvalidException;
    }
}