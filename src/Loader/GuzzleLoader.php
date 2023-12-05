<?php

namespace bpteam\QuickParserWizard\Loader;

use bpteam\QuickParserWizard\Enum\HttpMethod;
use bpteam\QuickParserWizard\ValueObject\Header;
use bpteam\QuickParserWizard\ValueObject\HeaderCollection;
use bpteam\QuickParserWizard\ValueObject\ContentLoaderResponse;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

class GuzzleLoader implements LoaderInterface
{
    public function __construct(
        protected readonly Client $client,
    ) {}

    public function load(string $url, HttpMethod $method = HttpMethod::GET, string $body = null, HeaderCollection $headerCollection = null): ContentLoaderResponse
    {
        $guzzleConfig = [
            RequestOptions::HEADERS => $this->getHeaders($headerCollection),
            RequestOptions::BODY => $body,
        ];

        $response = $this->client->request($method->value, $url, $guzzleConfig);

        return new ContentLoaderResponse(
            $response->getStatusCode(),
            $response->getBody()->getContents(),
            $this->convertToHeaderCollection($response->getHeaders()),
        );
    }


    private function getHeaders(?HeaderCollection $overwriteHeaders = null): array
    {
        $headers = [];
        $headers['User-Agent'] = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/119.0.0.0 Safari/537.36';

        foreach ($overwriteHeaders ?? [] as $header) {
            $headers[$header->name] = $header->value;
        }

        return $headers;
    }

    private function convertToHeaderCollection(array $responseHeaders): HeaderCollection
    {
        $headers = [];
        foreach ($responseHeaders as $name => $values) {
            foreach ($values as $value) {
                $headers[] = new Header($name, $value);
            }
        }

        return new HeaderCollection($headers);
    }
}