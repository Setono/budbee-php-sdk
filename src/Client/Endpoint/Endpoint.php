<?php

declare(strict_types=1);

namespace Setono\Budbee\Client\Endpoint;

use CuyZ\Valinor\Mapper\Source\Source;
use CuyZ\Valinor\MapperBuilder;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Setono\Budbee\Client\ClientInterface;

abstract class Endpoint implements EndpointInterface, LoggerAwareInterface
{
    protected ClientInterface $client;

    protected MapperBuilder $mapperBuilder;

    protected LoggerInterface $logger;

    public function __construct(ClientInterface $client, MapperBuilder $mapperBuilder)
    {
        $this->client = $client;
        $this->mapperBuilder = $mapperBuilder;
        $this->logger = new NullLogger();
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    protected function createSourceFromResponse(ResponseInterface $response): Source
    {
        $json = (string) $response->getBody();

        try {
            return Source::json($json);
        } catch (\Throwable $e) {
            $lastRequest = $this->client->getLastRequest();

            $message = sprintf('There was an error turning the JSON into a Source representation. The error was: %s.', $e->getMessage());

            if (null !== $lastRequest) {
                $message .= sprintf(' The request was %s %s', $lastRequest->getMethod(), (string) $lastRequest->getUri());
            }

            $message .= sprintf("The inputted JSON was:\n%s", $json);

            $this->logger->error($message);

            throw $e;
        }
    }
}
