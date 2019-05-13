# Scheduled Transactions

Scheduled transactions run in a batch once per day on the given date at `12:00:00 UTC`.

Use a DateTime object to set the value for the `scheduledAt` field. Use of a timezone is optional. It is recommended that you experiment with this value knowing that the API will process the date as described above.

```php
$scheduledAt = new DateTime('2018-03-20');
$scheduledAt->setTimezone(new DateTimeZone('EST'));

// or simply

$scheduledAt = new DateTime('2018-03-20', new DateTimeZone('EST'));
```

## Create a Scheduled Transaction

### via stored Payment Method

```php
$merchant          = new \StackPay\Payments\Structures\Merchant;
$merchant->id      = 12345;
$merchant->hashKey = 'merchant-hash-key-value';

$paymentMethod     = new \StackPay\Payments\Structures\PaymentMethod;
$paymentMethod->id = 12345;

$scheduledTransaction = new \StackPay\Payments\Structures\ScheduledTransaction;
$scheduledTransaction->merchant      = $merchant;
$scheduledTransaction->paymentMethod = $paymentMethod;
$scheduledTransaction->amount        = 5000;
$scheduledTransaction->currencyCode  = 'USD';
$scheduledTransaction->scheduledAt   = new DateTime('second friday');
$scheduledTransaction->externalId    = 'id-in-remote-system';

$request = (new \StackPay\Payments\Requests\v1\ScheduledTransactionRequest($scheduledTransaction))
            ->create();

$response = $request->send();
```

### via Payment Method Token

```php
$merchant          = new \StackPay\Payments\Structures\Merchant;
$merchant->id      = 12345;
$merchant->hashKey = 'merchant-hash-key-value';

$paymentMethod        = new \StackPay\Payments\Structures\PaymentMethod;
$paymentMethod->token = 'payment-method-token';

$scheduledTransaction = new \StackPay\Payments\Structures\ScheduledTransaction;
$scheduledTransaction->merchant      = $merchant;
$scheduledTransaction->paymentMethod = $paymentMethod;
$scheduledTransaction->amount        = 5000;
$scheduledTransaction->currencyCode  = 'USD';
$scheduledTransaction->scheduledAt   = new DateTime('second friday');
$scheduledTransaction->externalId    = 'id-in-remote-system';

$request = (new \StackPay\Payments\Requests\v1\ScheduledTransactionRequest($scheduledTransaction))
            ->create();

$response = $request->send();
```

### via Account Details

```php
$merchant          = new \StackPay\Payments\Structures\Merchant;
$merchant->id      = 12345;
$merchant->hashKey = 'merchant-hash-key-value';

$paymentMethod                  = new \StackPay\Payments\Structures\PaymentMethod;
$paymentMethod->accountNumber   = '4111111111111111';
$paymentMethod->expirationMonth = '12';
$paymentMethod->expirationYear  = '25';
$paymentMethod->cvv2            = '999';
$paymentMethod->billingName     = 'Stack Payman';
$paymentMethod->billingAddress1 = '5360 Legacy Drive #150';
$paymentMethod->billingCity     = 'Plano';
$paymentMethod->billingState    = 'TX';
$paymentMethod->billingZip      = '75024';
$paymentMethod->billingCountry  = \StackPay\Payments\Structures\Country::usa();

$scheduledTransaction                = new \StackPay\Payments\Structures\ScheduledTransaction;
$scheduledTransaction->merchant      = $merchant;
$scheduledTransaction->paymentMethod = $paymentMethod;
$scheduledTransaction->amount        = 5000;
$scheduledTransaction->currencyCode  = 'USD';
$scheduledTransaction->scheduledAt   = new DateTime('second friday');
$scheduledTransaction->externalId    = 'id-in-remote-system';

$request = (new \StackPay\Payments\Requests\v1\ScheduledTransactionRequest($scheduledTransaction))
            ->create();

$response = $request->send();
```

## Get Scheduled Transaction

```php
$scheduledTransaction     = new \StackPay\Payments\Structures\ScheduledTransaction;
$scheduledTransaction->id = 12345;

$request = (new \StackPay\Payments\Requests\v1\ScheduledTransactionRequest($scheduledTransaction))
            ->get();

$response = $request->send();
```

## Retry Scheduled Transaction

This method is only available when a scheduled transaction is in a `failed` state.

### with currently attached Payment Method

```php
$scheduledTransaction     = new \StackPay\Payments\Structures\ScheduledTransaction;
$scheduledTransaction->id = 12345;

$request = (new \StackPay\Payments\Requests\v1\ScheduledTransactionRequest($scheduledTransaction))
            ->retry();

$response = $request->send();
```

### with a different Payment Method

Example below shows the simplest example: via stored Payment Method (from above). A different can be attached using a token or account details as well by building the `$paymentMethod` object as shown in the Create examples above.

```php
$paymentMethod     = new \StackPay\Payments\Structures\PaymentMethod;
$paymentMethod->id = 12345;

$scheduledTransaction                = new \StackPay\Payments\Structures\ScheduledTransaction;
$scheduledTransaction->id            = 12345;
$scheduledTransaction->paymentMethod = $paymentMethod;

$request = (new \StackPay\Payments\Requests\v1\ScheduledTransactionRequest($scheduledTransaction))
            ->retry();

$response = $request->send();
```

## Delete Scheduled Transaction

This method is only available when a scheduled transaction is in a `scheduled` state.

```php
$scheduledTransaction     = new \StackPay\Payments\Structures\ScheduledTransaction;
$scheduledTransaction->id = 12345;

$request = (new \StackPay\Payments\Requests\v1\ScheduledTransactionRequest($scheduledTransaction))
            ->delete();

$response = $request->send();
```

## Get Daily Scheduled Transactions

Returns an array of scheduled transactions of a requested status during the requested date range.

```php
$paginatedScheduledTransactions = (new \StackPay\Payments\Structures\PaginatedScheduledTransactions())
    ->setBeforeDate(new DateTime('2016-01-01', new DateTimeZone('EST')))
    ->setAfterDate(new DateTime('2016-01-01', new DateTimeZone('EST')));
    // Optional
    ->setStatus('scheduled')    // Default: 'scheduled'
    ->setPerPage(10)            // Default: 10
    ->setCurrentPage(1)         // Default: 1

 $dailyScheduledTransactions = $stackpay->getDailyScheduledTransactions(
    $paginatedScheduledTransactions,
    $optionalIdempotencyKey
);
```

[Back to README](../README.md)
