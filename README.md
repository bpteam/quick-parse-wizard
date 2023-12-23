# Quick Parse Wizard

php classes for help to developers for setup scrappers with additional tools

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

## Tests

```
vendor/bin/phpunit
```
