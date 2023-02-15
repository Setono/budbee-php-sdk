<?php

declare(strict_types=1);

namespace Setono\Budbee\DTO;

final class Box
{
    public string $id;

    public string $name;

    public string $directions;

    public Address $address;

    public ?string $label;

    /**
     * todo figure out what this distance is? Meters probably?
     */
    public ?int $distance;

    public function __construct(
        string $id,
        string $name,
        string $directions,
        Address $address,
        string $label = null,
        int $distance = null
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->directions = $directions;
        $this->address = $address;
        $this->label = $label;
        $this->distance = $distance;
    }
}
