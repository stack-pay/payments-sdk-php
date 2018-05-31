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

### Composer

It is strongly recommended that you use [Composer](http://getcomposer.org) to
install this package and its dependencies. To do this, run the following command:

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

### Manual Installation

If you do not wish to use Composer, you can download the
[latest release](https://github.com/stack-pay/payments-sdk-php/releases). Then,
to use the bindings, include the `payments-sdk.php` file.

```php
require_once('/path/to/stack-pay/payments-sdk-php/lib/payments-sdk.php');
```

You will also need to download the dependencies and manually include them as
well. It is **strongly** recommended that you use Composer.

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

## Basic Structures

The basic structures used in the SDK.

#### Merchant

```php
$merchant = new StackPay\Payments\Structures\Merchant(
    $merchantId,
    $merchantHashKey
);

// or

$merchant = (new StackPay\Payments\Structures\Merchant())
    ->setID($merchantId)
    ->setHashKey($merchantHashKey);
```

#### Account

##### CreditCard

```php
$creditCard = new StackPay\Payments\Structures\CardAccount(
    $type, // StackPay\Payments\AccountTypes::AMEX, DISCOVER, MASTERCARD, VISA
    $accountNumber,
    $mmddExpirationDate,
    $cvv2,
    $savePaymentMethodBoolean
);

// or

$creditCard = (new StackPay\Payments\Structures\Account())
    ->setSavePaymentMethod($trueOrFalse)
    ->setType(StackPay\Payments\AccountTypes::VISA) // MASTERCARD, DISCOVER, AMEX
    ->setNumber($accountNumber)
    ->setExpireDate($mmddExpirationDate)
    ->setCvv2($cvv2);
```

##### Bank Account

```php
$bankAccount = new StackPay\Payments\Structures\BankAccount(
    $type, // StackPay\Payments\AccountTypes::CHECKING, SAVINGS
    $accountNumber,
    $routingNumber,
    $savePaymentMethodBoolean
);

// or

$bankAccount = (new StackPay\Payments\Structures\Account())
    ->setSavePaymentMethod($trueOrFalse)
    ->setType(StackPay\Payments\AccountTypes::CHECKING) // SAVINGS
    ->setNumber($accountNumber)
    ->setRoutingNumber($routingNumber);
```

#### Account Holder

```php
$accountHolder = new StackPay\Payments\Structures\AccountHolder(
    $accountHolderName,
    $billingAddress
);

// or

$accountHolder = (new StackPay\Payments\Structures\AccountHolder())
    ->setName($accountHolderName)
    ->setBillingAddress($billingAddress);
```

#### Address

```php
$address = new StackPay\Payments\Structures\Address(
    $addressLine1,
    $addressLine2,
    $city,
    $state,
    $postalCode,
    StackPay\Payments\Structures\Country::usa() // canada()
);

// or

$address = (new StackPay\Payments\Structures\Address())
    ->setAddress1($addressLine1)
    ->setAddress2($addressLine2)
    ->setCity($city)
    ->setState($stateAbbreviation)
    ->setPostalCode($postalCode)
    ->setCountry(StackPay\Payments\Structures\Country::usa());

// or

$address = (new StackPay\Payments\Structures\Address())
    ->setAddressLines("$addressLine1.$lineSeparator.$addressLine2", $lineSeparator)
    ->setCity($city)
    ->setState($stateAbbreviation)
    ->setPostalCode($zipCode)
    ->setCountry(StackPay\Payments\Structures\Country::usa());
```

#### Existing Customer

```php
$customer = new StackPay\Payments\Structures\Customer($customerId);

// or

$customer = (new StackPay\Payments\Structures\Customer())
    ->setId($customerId);
```

#### Existing Transaction (for use with Refunds and Voids)

```php
$transaction = new StackPay\Payments\Structures\Transaction($transactionId);

// or

$transaction = (new StackPay\Payments\Structures\Transaction())
    ->setId($transactionId);
```

## Generating and Processing Transactions

How to generate request objects using the factory class and how to use them to
process transactions.

#### Transaction Split

```php
$split = new StackPay\Payments\Structures\Split(
    $merchant,
    $amountInCents
);

// or

$split = (new StackPay\Payments\Structures\Split())
    ->setMerchant($merchant)
    ->setAmount($amountInCents);
```

#### Sale with Account Details

```php
$saleRequest = StackPay\Payments\Factories\Sale::withAccountDetails(
    StackPay\Payments\Structures\Account $account,
    StackPay\Payments\Structures\AccountHolder $accountHolder,
    StackPay\Payments\Structures\Merchant $merchant,
    $amountInCents,
    StackPay\Payments\Structures\Customer $optionalCustomer = null,
    StackPay\Payments\Structures\Split $optionalSplit = null,
    StackPay\Payments\Structures\Currency $optionalCurrencyOverride
);

$sale = $stackpay->processTransaction(
    $saleRequest,
    $optionalIdempotencyKey
);
```

#### Sale with Payment Method

```php
$saleRequest = StackPay\Payments\Factories\Sale::withPaymentMethod(
    StackPay\Payments\Structures\PaymentMethod $paymentMethod,
    StackPay\Payments\Structures\Merchant $merchant,
    $amountInCents,
    StackPay\Payments\Structures\Split $optionalSplit = null,
    StackPay\Payments\Structures\Currency $optionalCurrencyOverride
);

$sale = $stackpay->processTransaction(
    $saleRequest,
    $optionalIdempotencyKey
);
```

#### Void

```php
$voidRequest = StackPay\Payments\Factories\VoidTransaction::previousTransaction(
    StackPay\Payments\Structures\Transaction $previousTransaction
);

$void = $stackpay->processTransaction(
    $voidRequest,
    $optionalIdempotencyKey
);
```

#### Refund

```php
$refundRequest = StackPay\Payments\Factories\Refund::previousTransaction(
    StackPay\Payments\Structures\Transaction $previousSaleOrCaptureTransaction,
    $amountInCents
);

$refund = $stackpay->processTransaction(
    $refundRequest,
    $optionalIdempotencyKey
);
```

## Generating and Processing Scheduled Transactions

Set the Scheduled At date field for the transaction
```php
$scheduledAt = new DateTime('2018-03-20');
$scheduledAt->setTimezone(new DateTimeZone('America/New_York'));

// or simply

$scheduledAt = new DateTime('2018-03-20', new DateTimeZone('America/New_York'));
```

#### Schedule Transaction with Payment Method
```php
$paymentMethod = (new \StackPay\Payments\Structures\PaymentMethod())
    ->setID(50);

$scheduledTransaction = StackPay\Payments\Factories\ScheduleTransaction::withPaymentMethod(
    StackPay\Payments\Structures\PaymentMethod $paymentMethod,
    StackPay\Payments\Structures\Merchant $merchant,
    $amountInCents,
    \DateTime $scheduledAt,
    StackPay\Payments\Structures\Currency $optionalCurrencyOverride = null,
    StackPay\Payments\Structures\Split $split = null
);

$scheduledTransaction = $stackpay->createScheduledTransaction($scheduledTransaction);
```

#### Schedule Transaction with Payment Method Token
```php
$token = $stackpay->createTokenWithAccountDetails(
    StackPay\Payments\Structures\Account() $account,
    StackPay\Payments\Structures\AccountHolder() $accountHolder,
    StackPay\Payments\Structures\Customer $optionalCustomer = null
);

$scheduledTransaction = StackPay\Payments\Factories\ScheduleTransaction::withToken(
    StackPay\Payments\Structures\Token $token,
    StackPay\Payments\Structures\Merchant $merchant,
    $amountInCents,
    \DateTime $scheduledAt,
    StackPay\Payments\Structures\Currency $optionalCurrencyOverride = null,
    StackPay\Payments\Structures\Split $split = null
);

$scheduledTransaction = $stackpay->createScheduledTransaction($scheduledTransaction);
```

#### Schedule Transaction with Account Details
```php
$scheduledTransaction = StackPay\Payments\Factories\ScheduleTransaction::withAccountDetails(
    StackPay\Payments\Structures\Account $account,
    StackPay\Payments\Structures\AccountHolder $accountHolder,
    StackPay\Payments\Structures\Merchant $merchant,
    $amountInCents,
    \DateTime $scheduledAt,
    StackPay\Payments\Structures\Currency $optionalCurrencyOverride = null,
    StackPay\Payments\Structures\Split $split = null
);

$scheduledTransaction = $stackpay->createScheduledTransaction($scheduledTransaction);
```

## Documentation

Please see https://developer.mystackpay.com/docs for up-to-date documentation.

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
./vendor/bin/phpunit
```

Or to run an individual test file:

```bash
./vendor/bin/phpunit tests/SaleTest.php
```

## Support

- [https://developer.mystackpay.com/docs](https://developer.mystackpay.com/docs)
- [stackoverflow](http://stackoverflow.com/questions/tagged/stackpay)

## Contributing Guidelines

Please refer to [CONTRIBUTING.md](CONTRIBUTING.md)
