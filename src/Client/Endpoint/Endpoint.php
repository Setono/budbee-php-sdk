<?php

declare(strict_types=1);

namespace Setono\Budbee\Client\Endpoint;

use CuyZ\Valinor\Mapper\Source\Source;
use CuyZ\Valinor\MapperBuilder;
use Psr\Http\Message\ResponseInterface;
use Setono\Budbee\Client\ClientInterface;

abstract class Endpoint implements EndpointInterface
{
    protected ClientInterface $client;

    protected MapperBuilder $mapperBuilder;

    public function __construct(ClientInterface $client, MapperBuilder $mapperBuilder)
    {
        $this->client = $client;
        $this->mapperBuilder = $mapperBuilder;
    }

    protected function createSourceFromResponse(ResponseInterface $response): Source
    {
        return Source::json((string) $response->getBody());
    }
}
