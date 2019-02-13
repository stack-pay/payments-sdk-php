# Basic Structures

The basic structures used in the SDK.

## Merchant

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

## Account

### Card Account

```php
$cardAccount = new StackPay\Payments\Structures\CardAccount(
    $type, // StackPay\Payments\AccountTypes::AMEX, DISCOVER, MASTERCARD, VISA
    $accountNumber,
    $mmddExpirationDate,
    $cvv2,
    $savePaymentMethodBoolean
);

// or

$cardAccount = (new StackPay\Payments\Structures\Account())
    ->setSavePaymentMethod($trueOrFalse)
    ->setType(StackPay\Payments\AccountTypes::VISA) // MASTERCARD, DISCOVER, AMEX
    ->setNumber($accountNumber)
    ->setExpireDate($mmddExpirationDate)
    ->setCvv2($cvv2);
```

### Bank Account

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

## Account Holder

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

## Address

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

## Existing Customer

```php
$customer = new StackPay\Payments\Structures\Customer($customerId);

// or

$customer = (new StackPay\Payments\Structures\Customer())
    ->setId($customerId);
```

## Existing Transaction (for use with Refunds and Voids)

```php
$transaction = new StackPay\Payments\Structures\Transaction($transactionId);

// or

$transaction = (new StackPay\Payments\Structures\Transaction())
    ->setId($transactionId);
```

## Transaction Split

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

[Back to README](../README.md)
