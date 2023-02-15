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

```php
<?php

use Setono\Budbee\Client\Client;
use Setono\Budbee\DTO\Collection;
use Setono\Budbee\DTO\Box;

require_once '../vendor/autoload.php';

$client = new Client('API_KEY', 'API_SECRET');

$boxes = $client
    ->boxes()
    ->getAvailableLockers('DK', '9000')
;

foreach ($boxes as $box) {
    echo $box->name . "\n";
    echo $box->address->street . "\n";
    echo $box->address->zipCode . ' ' . $box->address->city . "\n";
    echo $box->address->countryCode . "\n\n";
}
```

will output something like:

```
Min Købmand Nørre Uttrup
Nørre Uttrup Torv 15
9400 Nørresundby
DK

Shell 7-Eleven Nørresundby
Østergade 27-29
9400 Nørresundby
DK

Next-Data.Dk
Østerbrogade 79
9400 Nørresundby
DK

...
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
