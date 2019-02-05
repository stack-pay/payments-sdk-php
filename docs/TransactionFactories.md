# Transaction Factories

> These methods are scheduled for deprecation.

Generate request objects using factory classes and process them.

This method of SDK usage is scheduled for deprecation. It will eventually be replaced with a process similar to what is shown in the Scheduled Transactions section below.

Prior to calling `processTransaction()`, you are able to add optional fields to your request objects:

```php
$saleRequest
    ->setInvoiceNumber('invoice_874196')
    ->setExternalId('transaction_174968')
    ->setComment1('Final payment on Invoice #874196')
    ->setComment2('Collected by Joe Salesman')
;
```

## Sale

### Sale with Account Details

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

### Sale with Payment Method

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

## Auth

### Auth with Account Details

```php
$authRequest = StackPay\Payments\Factories\Auth::withAccountDetails(
    StackPay\Payments\Structures\Account $account,
    StackPay\Payments\Structures\AccountHolder $accountHolder,
    StackPay\Payments\Structures\Merchant $merchant,
    $amountInCents,
    StackPay\Payments\Structures\Customer $optionalCustomer = null,
    StackPay\Payments\Structures\Split $optionalSplit = null,
    StackPay\Payments\Structures\Currency $optionalCurrencyOverride
);

$auth = $stackpay->processTransaction(
    $authRequest,
    $optionalIdempotencyKey
);
```

### Auth with Payment Method

```php
$authRequest = StackPay\Payments\Factories\Auth::withPaymentMethod(
    StackPay\Payments\Structures\PaymentMethod $paymentMethod,
    StackPay\Payments\Structures\Merchant $merchant,
    $amountInCents,
    StackPay\Payments\Structures\Split $optionalSplit = null,
    StackPay\Payments\Structures\Currency $optionalCurrencyOverride
);

$auth = $stackpay->processTransaction(
    $authRequest,
    $optionalIdempotencyKey
);
```

## Capture

```php
$captureRequest = StackPay\Payments\Factories\Capture::previousTransaction(
    StackPay\Payments\Structures\Transaction $previousTransaction,
    $amountInCents,
    StackPay\Payments\Structures\Split $optionalSplit = null, // used for setting splitAmount, splitMerchant can not be changed
);

$capture = $stackpay->processTransaction(
    $captureRequest,
    $optionalIdempotencyKey
);
```

## Void

```php
$voidRequest = StackPay\Payments\Factories\VoidTransaction::previousTransaction(
    StackPay\Payments\Structures\Transaction $previousTransaction
);

$void = $stackpay->processTransaction(
    $voidRequest,
    $optionalIdempotencyKey
);
```

## Refund

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

[Back to README](../README.md)
