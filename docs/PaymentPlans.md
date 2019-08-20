# Payment Plans

## Generate Hosted Page Access Token

```php
$merchant = (new StackPay\Payments\Structures\Merchant())
    ->setID($merchantId)
    ->setHashKey($merchantHashKey);

$optionalIdempotencyKey = null;

$hostedPageAccessToken = $stackpay->generateHostedPageAccessToken(
    $merchant,
    $optionalIdempotencyKey,
    $readOnly
);
```

## Copy Payment Plan

```php
$paymentPlanID = 1;

$merchant = (new StackPay\Payments\Structures\Merchant())
    ->setID($merchantId)
    ->setHashKey($merchantHashKey);

$copyPlan = (new Payments\Structures\PaymentPlan())
    ->setID($paymentPlanID)
    ->setMerchant($merchant);

$copyPlan = $stackpay->copyPaymentPlan($copyPlan);
```

## Edit Payment Plan

```php
$paymentPlanID = 1;

$merchant = (new StackPay\Payments\Structures\Merchant())
    ->setID($merchantId)
    ->setHashKey($merchantHashKey);

$paymentPlan = (new Payments\Structures\PaymentPlan())
    ->setID($paymentPlanID)
    ->setDownPaymentAmount(500)
    ->setMerchant($merchant);

$paymentPlan = $stackpay->editPaymentPlan($paymentPlan);
```

## Get Default Payment Plans

```php
$multiPlans = new Payments\Structures\MultiplePaymentPlans();
$multiPlans = $stackpay->getDefaultPaymentPlans($multiPlans);
```

## Get Merchant Payment Plans

```php
$merchant = (new StackPay\Payments\Structures\Merchant())
    ->setID($merchantId)
    ->setHashKey($merchantHashKey);

$merchantPlans = (new Payments\Structures\PaginatedPaymentPlans())
    ->setMerchant($merchant);

$merchantPlans = $stackpay->getMerchantPaymentPlans($merchantPlans);
```

[Back to README](../README.md)
