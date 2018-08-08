<?php

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class FunctionalTestCase extends TestCase
{
    public function setUp()
    {
        $this->StackPay = new StackPay\Payments\StackPay('public-key-12345', 'private-key-54321');
    }

    public function mockApiResponse($httpCode, array $body = null, $hash_key = null)
    {
        $json_body = json_encode($body, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        $body = [
            'Header' => [
                'Security' => [
                    'HashMethod'    => 'SHA-256',
                    'Hash'          => hash(
                        'sha256',
                        $json_body . ($hash_key ?: StackPay\Payments\StackPay::$privateKey)
                    ),
                ],
            ],
            'Body' => $body,
        ];

        $content = json_encode($body, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        $mock = new MockHandler([
            new Response($httpCode, [], $content)
        ]);

        $handler = HandlerStack::create($mock);

        $httpClient = new Client(['handler' => $handler]);

        $this->StackPay->setHttpClient($httpClient);
    }

    protected function assertResourceResponse()
    {
        $this->assertEquals($this->response->status(), 200);
        $this->assertTrue($this->response->success());
        $this->assertNull($this->response->error());
    }

    protected function assertEmptyResponse()
    {
        $this->assertEquals($this->response->status(), 200);
        $this->assertTrue($this->response->success());
        $this->assertNull($this->response->error());

        $this->assertEmpty($this->response->body());
    }

    protected function assertResourceNotFoundResponse()
    {
        $this->assertEquals($this->response->status(), 404);
        $this->assertNotTrue($this->response->success());
        $this->assertNotNull($this->response->error());
        $this->assertEquals($this->response->error()->getCode(), 400);
    }

    protected function emptyResponse()
    {
        return null;
    }

    protected function invalidInputResponse()
    {
        return [
            'error_code'    => '403',
            'error_message' => 'Input validation error. Details are available in \'errors\' section.',
            'errors'        => [],
        ];
    }

    protected function invalidMerchantResponse()
    {
        return [
            'error_code'    => '406',
            'error_message' => 'Merchant is invalid or non-existent.',
        ];
    }

    protected function invalidPaymentMethodResponse()
    {
        return [
            'error_code'    => '409',
            'error_message' => 'PaymentMethod is invalid or non-existent.',
        ];
    }

    protected function invalidTokenResponse()
    {
        return [
            'error_code'    => '404',
            'error_message' => 'Token is invalid or expired.',
        ];
    }

    protected function notFoundResponse()
    {
        return [
            'error_code'    => '400',
            'error_message' => 'The requested resource could not be found.',
        ];
    }
}
