# Payment Methods

## Create a stored Payment Method

```php
$account                        = new \StackPay\Payments\Structures\Account;
$account->type                  = \StackPay\Payments\AccountTypes::VISA;
$account->accountNumber         = '4111111111111111';
$account->expirationMonth       = '12';
$account->expirationYear        = '25';
$account->cvv2                  = '999';

$billingAddress                 = new \StackPay\Payments\Structures\Address;
$billingAddress->address1       = '5360 Legacy Drive #150';
$billingAddress->city           = 'Plano';
$billingAddress->state          = 'TX';
$billingAddress->postalCode     = '75024';
$billingAddress->country        = Structures\Country::usa();

$accountHolder                  = new \StackPay\Payments\Structures\AccountHolder;
$accountHolder->name            = 'Stack Testerman';
$accountHolder->billingAddress  = $billingAddress;

$paymentMethod                  = new Structures\PaymentMethod;
$paymentMethod->account         = $account;
$paymentMethod->accountHolder   = $accountHolder;

$request = (new \StackPay\Payments\Requests\v1\PaymentMethodRequest($paymentMethod))
    ->create();

$response = $request->send();
```

## Store a tokenized Payment Method

```php
$paymentMethod        = new \StackPay\Payments\Structures\PaymentMethod;
$paymentMethod->token = new \StackPay\Payments\Structures\Token('this-is-a-payment-token');

$request = (new \StackPay\Payments\Requests\v1\PaymentMethodRequest($this->paymentMethod))
    ->token();

$response = $request->send();
```

## Delete a stored Payment Method

```php
$paymentMethod     = new \StackPay\Payments\Structures\PaymentMethod;
$paymentMethod->id = 12345;

$request = (new \StackPay\Payments\Requests\v1\PaymentMethodRequest($paymentMethod))
            ->delete();

$response = $request->send();
```

[Back to README](../README.md)
