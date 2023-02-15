<?php

declare(strict_types=1);

namespace Setono\Budbee\Client;

use Nyholm\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface as HttpClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * @covers \Setono\Budbee\Client\Client
 */
final class ClientTest extends TestCase
{
    /**
     * @test
     */
    public function it_sends_expected_request(): void
    {
        $apiKey = 'username';
        $apiSecret = 'token';
        $expectedAuthorizationHeader = 'Basic ' . base64_encode("$apiKey:$apiSecret");

        $httpClient = new MockHttpClient();

        $client = new Client($apiKey, $apiSecret);
        $client->setHttpClient($httpClient);
        $client->get('endpoint/sub?param=value 1');

        self::assertNotNull($httpClient->lastRequest);
        self::assertNotNull($client->getLastResponse());
        self::assertNotNull($client->getLastRequest());
        self::assertSame('GET', $httpClient->lastRequest->getMethod());
        self::assertSame('https://api.budbee.com/endpoint/sub?param=value%201', (string) $httpClient->lastRequest->getUri());
        self::assertSame($expectedAuthorizationHeader, $httpClient->lastRequest->getHeaderLine('Authorization'));
    }

    /**
     * @test
     */
    public function it_returns_same_servicepoints_endpoint(): void
    {
        $client = new Client('username', 'token');
        $servicepointsEndpoint = $client->boxes();

        // this checks that we get the same instance for each call
        self::assertSame($servicepointsEndpoint, $client->boxes());
    }
}

final class MockHttpClient implements HttpClientInterface
{
    public ?RequestInterface $lastRequest = null;

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        $this->lastRequest = $request;

        return new Response();
    }
}
