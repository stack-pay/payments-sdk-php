# Main SDK Transaction Methods

> These methods are scheduled for deprecation.

The following is a sampling of direct SDK transaction method signatures. It is not exhaustive.

These methods build the request, send it, and process the response.

This method of SDK usage is scheduled for deprecation. It will eventually be replaced with a process similar to what is shown in the Scheduled Transactions section below.

## Sale

### via stored Payment Method

```php
$processedTransaction = $StackPay->saleWithPaymentMethod(
    Structures\PaymentMethod $paymentMethod,
    Structures\Merchant $merchant,
    $amount,
    Structures\Split $split = null,
    $idempotencyKey = null,
    $currency = null
);
```

### via Token

```php
$processedTransaction = $StackPay->saleWithToken(
    Structures\Token $token,
    Structures\Merchant $merchant,
    $amount,
    Structures\Split $split = null,
    $idempotencyKey = null,
    $currency = null
);
```

### via Account Details

```php
$processedTransaction = $StackPay->saleWithAccountDetails(
    Structures\Account $account,
    Structures\AccountHolder $accountHolder,
    Structures\Merchant $merchant,
    $amount,
    Structures\Customer $customer = null,
    Structures\Split $split = null,
    $idempotencyKey = null,
    $currency = null
);
```

### via MasterPass

```php
$processedTransaction = $StackPay->saleWithMasterPass(
    $masterPassTransactionId,
    Structures\Merchant $merchant,
    $amount,
    Structures\Customer $customer = null,
    Structures\Split $split = null,
    $idempotencyKey = null,
    $currency = null
);
```

[Back to README](../README.md)
