<?php

declare(strict_types=1);

namespace Setono\Budbee\Client;

use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Setono\Budbee\Client\Endpoint\BoxesEndpointInterface;
use Setono\Budbee\Exception\InternalServerErrorException;
use Setono\Budbee\Exception\NotFoundException;
use Setono\Budbee\Exception\UnexpectedStatusCodeException;

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
     * @throws ClientExceptionInterface if an error happens while processing the request
     * @throws InternalServerErrorException if the server reports an internal server error
     * @throws NotFoundException if the request results in a 404
     * @throws UnexpectedStatusCodeException if the status code is not between 200 and 299, and it's not any of the above
     */
    public function request(RequestInterface $request): ResponseInterface;

    /**
     * @throws ClientExceptionInterface if an error happens while processing the request
     * @throws InternalServerErrorException if the server reports an internal server error
     * @throws NotFoundException if the request results in a 404
     * @throws UnexpectedStatusCodeException if the status code is not between 200 and 299, and it's not any of the above
     */
    public function get(string $uri, array $query = []): ResponseInterface;

    public function boxes(): BoxesEndpointInterface;
}
