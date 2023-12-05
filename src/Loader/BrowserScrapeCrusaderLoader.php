<?php

namespace bpteam\QuickParserWizard\Loader;

use bpteam\QuickParserWizard\Enum\HttpMethod;
use bpteam\QuickParserWizard\ValueObject\Header;
use bpteam\QuickParserWizard\ValueObject\HeaderCollection;
use bpteam\QuickParserWizard\ValueObject\ContentLoaderResponse;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

/**
 * Wrapper for external API for send requests by real browser
 * by https://rapidapi.com/bpt22/api/scrapecrusader
 */
class BrowserScrapeCrusaderLoader implements LoaderInterface
{
    private Client $client;

    public function __construct(
        private readonly string $xRapidAPIKey,
        private readonly string $rapidApiHost = 'scrapecrusader.p.rapidapi.com',
    ) {
        $this->client = new Client();
    }

    public function load(string $url, HttpMethod $method = HttpMethod::GET, string $body = null, HeaderCollection $headerCollection = null): ContentLoaderResponse
    {
        $headers = [
            'content-type' => 'application/json',
            'X-RapidAPI-Key' => $this->xRapidAPIKey,
            'X-RapidAPI-Host' => $this->rapidApiHost,
        ];

        $guzzleConfig = [
            RequestOptions::HEADERS => $headers,
            RequestOptions::JSON => json_encode([
                'url' => $url,  // TODO implement method/body/headers/proxy for ScrapeCrusader
            ]),
        ];

        $response = $this->client->request('POST', 'https://' . $this->rapidApiHost . '/render/content', $guzzleConfig);
        $data = json_decode($response->getBody()->getContents(), true);
        return new ContentLoaderResponse(
            $data['code'],
            $data['content'],
            new HeaderCollection([]), // TODO implement headers provider from ScrapeCrusader
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