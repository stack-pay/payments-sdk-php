# Subscription

## Create Payment Plan Subscription

```php
$merchant = (new Payments\Structures\Merchant())
    ->setID($merchantId)
    ->setHashKey($merchantHash);

$paymentPlan = (new Payments\Structures\PaymentPlan())
    ->setID($paymentPlanId)
    ->setMerchant($merchant);

$paymentMethod = (new Payments\Structures\PaymentMethod())
    ->setAccount((new Payments\Structures\Account())
        ->setSavePaymentMethod(true)
        ->setType(Payments\AccountTypes::VISA)
        ->setNumber('4111111111111111')
        ->setExpireDate('0122')
        ->setCVV2('456')
    )
    ->setAccountHolder((new Payments\Structures\AccountHolder())
        ->setName('Test User')
        ->setBillingAddress((new Payments\Structures\Address())
            ->setAddress1('123 Test Ln')
            ->setCity('StackVille')
            ->setState('TX')
            ->setPostalCode('01234')
            ->setCountry('USA')
        )
    );
$paymentMethod = $stackpay->createPaymentMethod($paymentMethod);

$subscription = $stackpay->createPaymentPlanSubscription((new Payments\Structures\Subscription())
    ->setPaymentMethod($paymentMethod)
    ->setPaymentPlan($paymentPlan)
);
```

## Edit Payment Plan Subscription

Update the payment method on a subscription. Use the same object stack as Create, then specify the subscription ID.

```php
$subscriptionId = 1;

$paymentMethod = (new Payments\Structures\PaymentMethod())
    ->setID(1234);

$subscription = $stackpay->createPaymentPlanSubscription((new Payments\Structures\Subscription())
    ->setID($subscriptionId)
    ->setPaymentMethod($paymentMethod)
    ->setPaymentPlan($paymentPlan)
);

$updatedSubscription = $stackpay->editPaymentPlanSubscription($subscription);
```

[Back to README](../README.md)
