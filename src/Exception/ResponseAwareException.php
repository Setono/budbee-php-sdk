<?php

declare(strict_types=1);

namespace Setono\Budbee\Exception;

use Psr\Http\Message\ResponseInterface;

abstract class ResponseAwareException extends \RuntimeException
{
    private ResponseInterface $response;

    public function __construct(ResponseInterface $response)
    {
        $message = sprintf('The status code was: %d.', $response->getStatusCode());

        $body = trim((string) $response->getBody());
        if ('' !== $body) {
            $message .= sprintf(' The body was: %s.', $body);
        }

        parent::__construct(trim($message));

        $this->response = $response;
    }

    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }
}
