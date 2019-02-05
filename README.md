# Stack Pay™ - Payments SDK for PHP

[![Total Downloads](https://poser.pugx.org/stack-pay/payments-sdk-php/downloads.svg)](https://packagist.org/packages/stack-pay/payments-sdk-php)
[![Latest Stable Version](https://poser.pugx.org/stack-pay/payments-sdk-php/v/stable.svg)](https://packagist.org/packages/stack-pay/payments-sdk-php)
[![License](https://poser.pugx.org/stack-pay/payments-sdk-php/license.svg)](https://packagist.org/packages/stack-pay/payments-sdk-php)

The Stack Pay Payments SDK for PHP is an open source library through which your
PHP application can easily interact with the
[Stack Pay API](https://developer.mystackpay.com/docs).

**Note:** This release utilizes Stack Pay API v1. There are substantial
differences between this version of the client library and subsequent versions.
Please be mindful of this when upgrading.

## Requirements

PHP 5.4.0 (or higher)

## Dependencies

### PHP Curl Class 7.2.0 (or higher)

This library also requires `'ext-curl': '*'`.

## Installation

### Composer (recommended)

It is strongly recommended that you use [Composer](http://getcomposer.org) to
install this package and its dependencies. Some methods utilize `GuzzleHttp`. If you do not install via Composer, these methods will be difficult to use.

To install via Composer, run the following command:

```bash
composer require stack-pay/payments-sdk-php
```

You can also manually add this dependency to your `composer.json` file:

```json
{
    "require": {
        "stack-pay/payments-sdk-php": "~1.0.0"
    }
}
```

To use the bindings, use Composer's
[autoload](https://getcomposer.org/doc/00-intro.md#autoloading):

```php
require_once('vendor/autoload.php');
```

### Manual Installation (not recommended)

If you do not wish to use Composer, you can download the
[latest release](https://github.com/stack-pay/payments-sdk-php/releases). Then,
to use the bindings, include the `payments-sdk.php` file.

```php
require_once('/path/to/stack-pay/payments-sdk-php/lib/payments-sdk.php');
```

You will also need to download the dependencies and manually include them, which can be extremely cumbersome. It is **strongly** recommended that you use Composer.

## Instantiating the SDK

```php
$stackpay = new StackPay\Payments\StackPay($yourPublicKey, $yourPrivateKey);
```

This will create a StackPay class instance in *PRODUCTION* mode with *USD* as the default currency.

To enable development/testing mode, you should then use:

```php
$stackpay->enableTestMode();
```

To change currency:

```php
$stackpay->setCurrency('CAD');
```

## Documentation

* [Basic Structures](docs/BasicStructures.md)
* [Main SDK Transaction Methods](docs/MainTransactionMethods.md)
* [Transaction Factories](docs/TransactionFactories.md)
* Request-Focused Implementation
  * [Payment Methods](docs/PaymentMethods.md)
  * [Scheduled Transactions](docs/ScheduledTransactions.md)
  * [Payment Plans](docs/PaymentPlans.md)
  * [Subscription](docs/Subscription.md)
* [StackPay API Docs](https://developer.mystackpay.com/docs) (external site)

### Request-Focused Implementation

The examples in these docs are recommended when the SDK is installed via Composer. These methods use `GuzzleHttp` which is very difficult to use without a good autoloader.

You can directly interact with the response returned by these methods using the `->body()` method, which is the JSON-decoded `Body` element of the response payload as a `stdClass` PHP object.

```php
$response = $request->send();

echo $response->body()->ID;
```

You can check the response for success using method `success()`. If `success()` returns `false`, then you can use `error()` to access `code`, `messages`, and `errors` attributes.

```php
$response = $request->send();

if (! $response->success()) {
    echo $response->error()->code."\n"; // the API response error code
    echo $response->error()->message."\n"; // the API response error message
    print_r($response->error()->errors); // populated when the request body does not pass validation
}
```

## Development

Install dependencies:

```bash
composer install
```

## Tests

Install dependencies as mentioned above (which will resolve
[PHPUnit](http://packagist.org/packages/phpunit/phpunit)), then you can run the
test suite:

```bash
composer test
```

If you plan to use these tests, it is highly recommended that you familiarize yourself with PHPUnit as well as the `phpunit.xml` configuration file included with this package.

## Support

* [stackoverflow](http://stackoverflow.com/questions/tagged/stackpay)

## Contributing Guidelines

Please refer to [CONTRIBUTING.md](CONTRIBUTING.md) (coming soon)
