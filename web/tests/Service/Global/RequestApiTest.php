<?php

namespace App\Tests\Service\Global;

use App\Service\Global\RequestApi;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class RequestApiTest extends TestCase
{
    private $httpClientMock;
    private $requestApiService;

    protected function setUp(): void
    {
        $this->httpClientMock = $this->createMock(HttpClientInterface::class);
        $this->requestApiService = new RequestApi($this->httpClientMock);
    }

    public function testSend(): void
    {
        $responseMock = $this->createMock(ResponseInterface::class);

        $responseMock
            ->method('toArray')
            ->willReturn(['success' => true, 'message' => 'Request successful']);

        $this->httpClientMock
            ->method('request')
            ->with(
                $this->equalTo('GET'),
                $this->equalTo('https://api.example.com/data'),
                $this->equalTo([
                    'headers' => [
                        'Authorization' => 'Bearer test-token',
                        'Accept' => 'application/json',
                    ],
                ])
            )
            ->willReturn($responseMock);

        $result = $this->requestApiService->send(
            'GET',
            'https://api.example.com/data',
            [
                'Authorization' => 'Bearer test-token',
                'Accept' => 'application/json',
            ]
        );

        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        $this->assertTrue($result['success']);
        $this->assertEquals('Request successful', $result['message']);
    }

    public function testSendWithErrorResponse(): void
    {
        $responseMock = $this->createMock(ResponseInterface::class);

        $responseMock
            ->method('toArray')
            ->willThrowException(new \Exception('Error parsing response'));

        $this->httpClientMock
            ->method('request')
            ->willReturn($responseMock);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Error parsing response');

        $this->requestApiService->send(
            'GET',
            'https://api.example.com/error',
            [
                'Authorization' => 'Bearer test-token',
                'Accept' => 'application/json',
            ]
        );
    }
}
