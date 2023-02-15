# Budbee PHP SDK

[![Latest Version][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE)
[![Build Status][ico-github-actions]][link-github-actions]
[![Code Coverage][ico-code-coverage]][link-code-coverage]
[![Mutation testing][ico-infection]][link-infection]

Consume the [Budbee API](https://developer.budbee.com) in PHP.

## Installation

```bash
composer require setono/budbee-php-sdk
```

## Usage

### Get available lockers

```php
<?php

use Setono\Budbee\Client\Client;
use Setono\Budbee\DTO\Collection;
use Setono\Budbee\DTO\Box;

require_once '../vendor/autoload.php';

$client = new Client('API_KEY', 'API_SECRET');
// Set a logger to log more details about exceptions thrown
// $client->setLogger($logger);

// Enable sandbox mode to test your integration
// $client->setSandbox();

$boxes = $client
    ->boxes()
    ->getAvailableLockers('DK', '1159')
;

/** @var Box $box */
foreach ($boxes as $box) {
    echo $box->name . " ($box->id)\n";
    echo $box->address->street . "\n";
    echo $box->address->postalCode . ' ' . $box->address->city . "\n";
    echo $box->address->country . "\n\n";
}
```

will output something like:

```
Budbee CPH Office (BOX0012)
Ehlersvej 11
2900 Hellerup
DK
```

### Other requests

If the endpoint or method you want to call isn't present yet, you have two options: 1) Create a PR and add the missing parts or 2) use the generic `request` method:

```php
<?php

use Setono\Budbee\Client\Client;

require_once '../vendor/autoload.php';

$client = new Client('API_KEY', 'API_SECRET');

/** @var \Psr\Http\Message\ResponseInterface $response */
$response = $client->request(/** @var \Psr\Http\Message\RequestInterface $request */ $request);
```

## Production usage

Internally this library uses the [CuyZ/Valinor](https://github.com/CuyZ/Valinor) library which is particularly well suited
for turning API responses in DTOs. However, this library has some overhead and works best with a cache enabled.

When you instantiate the `Client` you can provide a `MapperBuilder` instance. Use this opportunity to set a cache:

```php
<?php

use CuyZ\Valinor\Cache\FileSystemCache;
use CuyZ\Valinor\MapperBuilder;
use Setono\Budbee\Client\Client;
use Setono\Budbee\DTO\Collection;
use Setono\Budbee\DTO\Box;

require_once '../vendor/autoload.php';

$cache = new FileSystemCache('path/to/cache-directory');
$client = new Client('API_KEY', 'API_SECRET', (new MapperBuilder())->withCache($cache));
```

You can read more about it here: [Valinor: Performance and caching](https://valinor.cuyz.io/latest/other/performance-and-cache/).

[ico-version]: https://poser.pugx.org/setono/budbee-php-sdk/v/stable
[ico-license]: https://poser.pugx.org/setono/budbee-php-sdk/license
[ico-github-actions]: https://github.com/Setono/budbee-php-sdk/workflows/build/badge.svg
[ico-code-coverage]: https://codecov.io/gh/Setono/budbee-php-sdk/branch/master/graph/badge.svg
[ico-infection]: https://img.shields.io/endpoint?style=flat&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2FSetono%2Fbudbee-php-sdk%2Fmaster

[link-packagist]: https://packagist.org/packages/setono/budbee-php-sdk
[link-github-actions]: https://github.com/Setono/budbee-php-sdk/actions
[link-code-coverage]: https://codecov.io/gh/Setono/budbee-php-sdk
[link-infection]: https://dashboard.stryker-mutator.io/reports/github.com/Setono/budbee-php-sdk/master
