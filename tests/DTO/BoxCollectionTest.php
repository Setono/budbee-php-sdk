<?php

declare(strict_types=1);

namespace Setono\Budbee\DTO;

use PHPUnit\Framework\TestCase;

/**
 * @covers \Setono\Budbee\DTO\BoxCollection
 */
final class BoxCollectionTest extends TestCase
{
    /**
     * @test
     */
    public function it_has_collection_traits(): void
    {
        $servicepoint = new Box(
            'id',
            'name',
            'directions',
            'label',
            50,
            new Address('street', '1234', 'city', 'DK', new Coordinate(123.4, 123.4))
        );
        $collection = new BoxCollection([$servicepoint]);

        self::assertCount(1, $collection);
        self::assertFalse($collection->isEmpty());

        foreach ($collection as $item) {
            self::assertSame($servicepoint, $item);
        }
    }
}
