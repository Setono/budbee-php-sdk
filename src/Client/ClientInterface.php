<?php

declare(strict_types=1);

namespace Setono\Budbee\Client;

use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Setono\Budbee\Client\Endpoint\BoxesEndpointInterface;

interface ClientInterface
{
    public function setSandbox(bool $sandbox = true): void;

    /**
     * Returns the last request sent to the API if any requests has been sent
     */
    public function getLastRequest(): ?RequestInterface;

    /**
     * Returns the last response from the API, if any responses has been received
     */
    public function getLastResponse(): ?ResponseInterface;

    /**
     * @throws ClientExceptionInterface If an error happens while processing the request.
     */
    public function request(RequestInterface $request): ResponseInterface;

    /**
     * @throws ClientExceptionInterface If an error happens while processing the request.
     */
    public function get(string $uri): ResponseInterface;

    public function boxes(): BoxesEndpointInterface;
}
