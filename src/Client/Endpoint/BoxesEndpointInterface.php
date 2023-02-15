<?php

declare(strict_types=1);

namespace Setono\Budbee\Client\Endpoint;

use Setono\Budbee\DTO\Box;
use Setono\Budbee\DTO\BoxCollection;

interface BoxesEndpointInterface extends EndpointInterface
{
    /**
     * If no boxes exists with the given criteria the returned collection will be empty
     */
    public function getAvailableLockers(
        string $countryCode,
        string $postalCode,
        int $collectionPointId = null,
        string $language = null,
        int $width = null,
        int $height = null,
        int $length = null,
        \DateTimeInterface $readyToShip = null
    ): BoxCollection;

    /**
     * Returns null if a box / locker doesn't exist with the given identifier
     */
    public function getLockerByIdentifier(string $identifier): ?Box;
}
