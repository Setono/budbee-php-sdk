<?php

declare(strict_types=1);

namespace Setono\Budbee\Exception;

use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Message\ResponseInterface;

final class ResponseException extends \RuntimeException implements ClientExceptionInterface
{
    private ResponseInterface $response;

    public function __construct(string $message, ResponseInterface $response)
    {
        $body = trim((string) $response->getBody());
        if ('' !== $body) {
            $message .= ' The response body was: ' . $body;
        }

        parent::__construct($message);

        $this->response = $response;
    }

    public static function assertStatusCode(ResponseInterface $response): void
    {
        if ($response->getStatusCode() < 200 || $response->getStatusCode() >= 300) {
            throw new self(sprintf('The status code was: %d.', $response->getStatusCode()), $response);
        }
    }

    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }
}
