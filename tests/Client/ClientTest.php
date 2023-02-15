<?php

declare(strict_types=1);

namespace Setono\Budbee\Client;

use CuyZ\Valinor\MapperBuilder;
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
        $client->get('/endpoint/sub', [
            'empty' => null,
            'param1' => 'value 1',
            'param2' => 'value 2',
            'date' => \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2023-02-15 11:50:00'),
        ]);

        self::assertNotNull($httpClient->lastRequest);
        self::assertNotNull($client->getLastResponse());
        self::assertNotNull($client->getLastRequest());
        self::assertSame('GET', $httpClient->lastRequest->getMethod());
        self::assertSame(
            'https://api.budbee.com/endpoint/sub?param1=value%201&param2=value%202&date=2023-02-15T11%3A50%3A00%2B00%3A00',
            (string) $httpClient->lastRequest->getUri()
        );
        self::assertSame($expectedAuthorizationHeader, $httpClient->lastRequest->getHeaderLine('Authorization'));
    }

    /**
     * @test
     */
    public function it_returns_same_boxes_endpoint(): void
    {
        $client = new Client('apiKey', 'apiSecret');
        $boxesEndpoint = $client->boxes();

        // this checks that we get the same instance for each call
        self::assertSame($boxesEndpoint, $client->boxes());
    }

    /**
     * @test
     */
    public function it_returns_mapper_builder(): void
    {
        $client = new Client('apiKey', 'apiSecret');

        $mapperBuilder = $client->getMapperBuilder();

        self::assertSame($mapperBuilder, $client->getMapperBuilder());
    }

    /**
     * @test
     */
    public function it_allows_to_set_the_mapper_builder(): void
    {
        $client = new Client('apiKey', 'apiSecret');

        $mapperBuilder = $client->getMapperBuilder();
        $client->setMapperBuilder(new MapperBuilder());

        self::assertNotSame($mapperBuilder, $client->getMapperBuilder());
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
