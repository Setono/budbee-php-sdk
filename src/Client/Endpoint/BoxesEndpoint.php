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
        // https://api.budbee.com/boxes/postalcodes/validate/{countryCode}/{postalCode}?collectionPointId={collectionPoint}&language={language}&width={width}&height={height}&length={length}&readyToShip={readyToShip}
        $uri = sprintf('boxes/postalcodes/validate/%s/%s', $countryCode, $postalCode);

        $query = http_build_query(array_filter([
            'collectionPointId' => $collectionPointId,
            'language' => $language,
            'width' => $width,
            'height' => $height,
            'length' => $length,
            'readyToShip' => $readyToShip ? $readyToShip->format(\DATE_ATOM) : null,
        ]));

        if ('' !== $query) {
            $uri .= '?' . $query;
        }

        $response = $this->client->get($uri);

        return $this->mapperBuilder->mapper()
            ->map(BoxCollection::class, $this->createSourceFromResponse($response)->map([
                'lockers' => 'boxes',
            ]));
    }
}
