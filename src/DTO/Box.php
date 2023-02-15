<?php

declare(strict_types=1);

namespace Setono\Budbee\DTO;

final class Box
{
    public string $id;

    public string $name;

    public string $directions;

    public string $label;

    /**
     * todo figure out what this distance is? Meters probably?
     */
    public int $distance;

    public Address $address;

    public function __construct(
        string $id,
        string $name,
        string $directions,
        string $label,
        int $distance,
        Address $address
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->directions = $directions;
        $this->label = $label;
        $this->distance = $distance;
        $this->address = $address;
    }
}
