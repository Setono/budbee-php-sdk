<?php

declare(strict_types=1);

namespace Setono\Budbee\DTO;

/**
 * @implements \IteratorAggregate<int, Box>
 */
final class BoxCollection implements \IteratorAggregate, \Countable
{
    /** @var list<Box> */
    public array $boxes;

    /**
     * @param list<Box> $boxes
     */
    public function __construct(array $boxes)
    {
        $this->boxes = $boxes;
    }

    /**
     * @return Box[]
     *
     * @psalm-return \ArrayIterator<int<0, max>, Box>
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->boxes);
    }

    public function count(): int
    {
        return count($this->boxes);
    }

    public function isEmpty(): bool
    {
        return [] === $this->boxes;
    }
}
