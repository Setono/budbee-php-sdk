<?php

declare(strict_types=1);

namespace Setono\Budbee\DTO;

final class Address
{
    public string $street;

    public string $postalCode;

    public string $city;

    public string $country;

    public Coordinate $coordinate;

    public function __construct(
        string $street,
        string $postalCode,
        string $city,
        string $country,
        Coordinate $coordinate
    ) {
        $this->street = $street;
        $this->postalCode = $postalCode;
        $this->city = $city;
        $this->country = $country;
        $this->coordinate = $coordinate;
    }
}
