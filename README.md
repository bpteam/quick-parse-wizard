# Quick Parse Wizard

Project designed to simplify the process of creating web scraping, making it accessible and effective for both experienced developers and users with a weak technical level.

## Installation

```
composer require bpteam/quick-parse-wizard
```

## Features

- Loader
  - Run requests by real chrome browser with javascript render
  - Send requests with retry and response validation by response code or regexp for response content
- RegExpGenerator 
  - generate regular expression for html tag 
  - generate regular expression for css selectors
- TextExtractor
  - extract text from html tag without dom parsing
  - divide text to sentences 
- ApiKeyRotator
  - rotate api keys by requests count with limit for each key by time and price 

## Examples

### Loader

Loader with retry

```php
use bpteam\QuickParseWizard\Loader\GuzzleWithRetryLoader;

$loader = GuzzleWithRetryLoader::create(
    defaultGuzzleOptions: [], // For more information see https://docs.guzzlephp.org/en/stable/request-options.html
    defaultRetryOptions: [ // For more information see https://github.com/caseyamcl/guzzle_retry_middleware?tab=readme-ov-file#options
        'retry_on_status' => ['503', '502', '429'],
        'retry_on_timeout' => true,
    ],   
);

$response = $loader->load('https://example.com');

echo $response->body;
```

Loader with javascript render

```php
use bpteam\QuickParserWizard\Loader\BrowserScrapeCrusaderLoader;

$loader = new BrowserScrapeCrusaderLoader(
    xRapidAPIKey: 'your-api-key-from-rapidapi.com', // For more information see https://rapidapi.com/bpt22/api/scrapecrusader
);

$response = $loader->load('https://example.com');

echo $response->body;
```

### ApiKeyRotator

ApiKeyArrayProvider for use in one process

```php
use bpteam\QuickParserWizard\ApiKeyRotator\ApiKeyArrayProvider;

$apiKey = new ApiKeyArrayProvider();
$apiKey->add('my_key_name', new Key('MY_FIRST_API_KEY'), 1000, new \DateInterval('PT1M'));
$apiKey->add('my_key_name_2', new Key('MY_SECOND_API_KEY'), 1000, new \DateInterval('PT1M'));

$key = $apiKey->find();

echo $key->getCredentials(); // MY_FIRST_API_KEY
```

## Tests

```
vendor/bin/phpunit
```
