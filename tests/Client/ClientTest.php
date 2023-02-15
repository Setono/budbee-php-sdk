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
        $username = 'username';
        $token = 'token';
        $expectedAuthorizationHeader = 'Basic ' . base64_encode("$username:$token");

        $httpClient = new MockHttpClient();

        $client = new Client($username, $token);
        $client->setHttpClient($httpClient);
        $client->get('servicepoints/gls?street=Highway 1');

        self::assertNotNull($httpClient->lastRequest);
        self::assertNotNull($client->getLastResponse());
        self::assertNotNull($client->getLastRequest());
        self::assertSame('GET', $httpClient->lastRequest->getMethod());
        self::assertSame('https://api.budbee.dk/servicepoints/gls?street=Highway%201', (string) $httpClient->lastRequest->getUri());
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
