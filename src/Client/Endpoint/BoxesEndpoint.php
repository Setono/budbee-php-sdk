<?php

declare(strict_types=1);

namespace Setono\Budbee\Client\Endpoint;

use Setono\Budbee\DTO\BoxCollection;

final class BoxesEndpoint extends Endpoint implements BoxesEndpointInterface
{
    public function getAvailableLockers(
        string $countryCode,
        string $postalCode,
        int $collectionPointId = null,
        string $language = null,
        int $width = null,
        int $height = null,
        int $length = null,
        \DateTimeInterface $readyToShip = null
    ): BoxCollection {
        $response = $this->client->get(sprintf('boxes/postalcodes/validate/%s/%s', $countryCode, $postalCode), [
            'collectionPointId' => $collectionPointId,
            'language' => $language,
            'width' => $width,
            'height' => $height,
            'length' => $length,
            'readyToShip' => $readyToShip,
        ]);

        return $this->mapperBuilder->mapper()
            ->map(BoxCollection::class, $this->createSourceFromResponse($response)->map([
                'lockers' => 'boxes',
            ]));
    }
}
