# Linear Url Parser

Library for parsing sites with linear URLs.

What does it means **Linear URLs**?
Some sites automatically create a new public URL with some content
using linear strategy, for example, if current url is `https://site.com/s/123abx`
the next URLs will be:
* `https://site.com/s/123aby`
* `https://site.com/s/123abz`
* `https://site.com/s/123aca`
* `https://site.com/s/123acb`

Often, this approach is used by websites to create screenshots or short links.

**Linear Url Parser** is able to generate a list of next or previous URLs based on provided URL and
parse and save specific content (for example image) using RegExp selector.



### Documentation

Compatible with PHP 7.1

#### Usage example

```php
<?php

require_once 'vendor/autoload.php';

// Init parameters
$baseUrl = 'https://site.com/s/';
$query = '123abx'; // linear changeable URL part

$imageSelector = '/<img\ssrc="([^"]+)"\sid="screenshot"/is'; // RegExp expression
$imageFolderPath = 'local/path/to/folder';

$bulkSize = 100;
$reverse = false;

// Min and Max delay in milliseconds between requests
$timeoutMin = 200;
$timeoutMax = 500;

// Ranges of characters that are used in the URL
$ranges = [
    ['0', '9'],
    ['a', 'z']
];

// Init objects
$urlGenerator = new LinearUrlParser\UrlGenerator(
    $baseUrl,
    $query,
    $ranges,
    $reverse
);

$parser = new LinearUrlParser\Parser(
    $imageFolderPath,
    $imageSelector,
    $timeoutMin,
    $timeoutMax
);

$urlsBulk = $urlGenerator->generateLinearUrlBulk($bulkSize);
foreach ($urlsBulk as $query => $url) {
    $parser->parseImage($query, $url);
}
```
