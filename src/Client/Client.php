<?php

declare(strict_types=1);

namespace Setono\Budbee\Client;

use CuyZ\Valinor\MapperBuilder;
use Http\Discovery\Psr17FactoryDiscovery;
use Http\Discovery\Psr18ClientDiscovery;
use Psr\Http\Client\ClientInterface as HttpClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Setono\Budbee\Client\Endpoint\BoxesEndpoint;
use Setono\Budbee\Client\Endpoint\BoxesEndpointInterface;
use Setono\Budbee\Exception\InternalServerErrorException;
use Setono\Budbee\Exception\NotFoundException;
use Setono\Budbee\Exception\UnexpectedStatusCodeException;

final class Client implements ClientInterface, LoggerAwareInterface
{
    private bool $sandbox = false;

    private ?RequestInterface $lastRequest = null;

    private ?ResponseInterface $lastResponse = null;

    private ?BoxesEndpointInterface $boxesEndpoint = null;

    private ?HttpClientInterface $httpClient = null;

    private ?RequestFactoryInterface $requestFactory = null;

    private LoggerInterface $logger;

    private string $apiKey;

    private string $apiSecret;

    private ?MapperBuilder $mapperBuilder = null;

    public function __construct(string $apiKey, string $apiSecret)
    {
        $this->logger = new NullLogger();
        $this->apiKey = $apiKey;
        $this->apiSecret = $apiSecret;
    }

    public function setSandbox(bool $sandbox = true): void
    {
        $this->sandbox = $sandbox;
    }

    public function getLastRequest(): ?RequestInterface
    {
        return $this->lastRequest;
    }

    public function getLastResponse(): ?ResponseInterface
    {
        return $this->lastResponse;
    }

    public function request(RequestInterface $request): ResponseInterface
    {
        $request = $request->withHeader(
            'Authorization',
            sprintf('Basic %s', base64_encode($this->apiKey . ':' . $this->apiSecret))
        );

        $this->lastRequest = $request;
        $this->lastResponse = $this->getHttpClient()->sendRequest($this->lastRequest);

        self::assertStatusCode($this->lastResponse);

        return $this->lastResponse;
    }

    public function get(string $uri, array $query = []): ResponseInterface
    {
        $q = http_build_query(array_map(static function ($element) {
            return $element instanceof \DateTimeInterface ? $element->format(\DATE_ATOM) : $element;
        }, $query), '', '&', \PHP_QUERY_RFC3986);

        $url = sprintf('%s/%s%s', $this->getBaseUri(), ltrim($uri, '/'), '' === $q ? '' : '?' . $q);

        $request = $this->getRequestFactory()->createRequest('GET', $url);

        return $this->request($request);
    }

    public function boxes(): BoxesEndpointInterface
    {
        if (null === $this->boxesEndpoint) {
            $this->boxesEndpoint = new BoxesEndpoint($this, $this->getMapperBuilder());
            $this->boxesEndpoint->setLogger($this->logger);
        }

        return $this->boxesEndpoint;
    }

    public function setMapperBuilder(MapperBuilder $mapperBuilder): void
    {
        $this->mapperBuilder = $mapperBuilder;
    }

    public function getMapperBuilder(): MapperBuilder
    {
        if (null === $this->mapperBuilder) {
            $this->mapperBuilder = (new MapperBuilder())
                ->enableFlexibleCasting()
                ->allowSuperfluousKeys()
            ;
        }

        return $this->mapperBuilder;
    }

    public function setHttpClient(?HttpClientInterface $httpClient): void
    {
        $this->httpClient = $httpClient;
    }

    public function setRequestFactory(?RequestFactoryInterface $requestFactory): void
    {
        $this->requestFactory = $requestFactory;
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    private function getBaseUri(): string
    {
        return $this->sandbox ? 'https://sandbox.api.budbee.com' : 'https://api.budbee.com';
    }

    private function getHttpClient(): HttpClientInterface
    {
        if (null === $this->httpClient) {
            $this->httpClient = Psr18ClientDiscovery::find();
        }

        return $this->httpClient;
    }

    private function getRequestFactory(): RequestFactoryInterface
    {
        if (null === $this->requestFactory) {
            $this->requestFactory = Psr17FactoryDiscovery::findRequestFactory();
        }

        return $this->requestFactory;
    }

    private static function assertStatusCode(ResponseInterface $response): void
    {
        $statusCode = $response->getStatusCode();

        if ($statusCode >= 200 && $statusCode < 300) {
            return;
        }

        NotFoundException::assert($response);
        InternalServerErrorException::assert($response);

        throw new UnexpectedStatusCodeException($response);
    }
}
