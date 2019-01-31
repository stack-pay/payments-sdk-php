# Payment Plans

## Generate Hosted Page Access Token

```php
$merchant = new StackPay\Payments\Structures\Merchant(
    $merchantId,
    $merchantHashKey
);

// or

$merchant = (new StackPay\Payments\Structures\Merchant())
    ->setID($merchantId)
    ->setHashKey($merchantHashKey);

// ---

$hostedPageAccessToken = $stackpay->generateHostedPageAccessToken(
    $merchant,
    $optionalIdempotencyKey
);
```

## Copy Payment Plan

## Edit Payment Plan

## Get Default Payment Plans

## Get Merchant Payment Plans

[Back to README](../README.md)
