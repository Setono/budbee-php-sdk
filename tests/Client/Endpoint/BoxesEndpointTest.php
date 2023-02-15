<?php

declare(strict_types=1);

namespace Setono\Budbee\Client\Endpoint;

use Nyholm\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Http\Client\ClientInterface as HttpClientInterface;
use Setono\Budbee\Client\Client;
use Setono\Budbee\Client\ClientInterface;
use Setono\Budbee\DTO\Box;

final class BoxesEndpointTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     */
    public function it_gets_boxes_by_country_code_and_postal_code(): void
    {
        $client = $this->createClient(<<<JSON
{
  "lockers": [
    {
      "id": "BOX0001",
      "address": {
        "street": "Alströmergatan 39",
        "postalCode": "11247",
        "city": "Stockholm",
        "country": "SE",
        "coordinate": {
            "latitude": 59.336125,
            "longitude": 18.028590
        }
      },
      "estimatedDelivery": "2020-02-12T14:00:00Z",
      "cutoff": "2020-02-12T09:00:00Z",
      "distance": 50,
      "name": "Budbee Kontorsbox",
      "directions": "Boxen är direkt till vänster i entrén",
      "label": "Budbee Kontorsbox (imorgon 16:00)",
      "openingHours": {
        "periods": [
          { "open": { "day": "MONDAY", "time": "08:00" }, "close": {"day": "MONDAY", "time": "19:00" } },
          { "open": { "day": "TUESDAY", "time": "08:00" }, "close": {"day": "TUESDAY", "time": "19:00" } },
          { "open": { "day": "WEDNESDAY", "time": "08:00" }, "close": {"day": "WEDNESDAY", "time": "19:00" } },
          { "open": { "day": "THURSDAY", "time": "08:00" }, "close": {"day": "THURSDAY", "time": "19:00" } },
          { "open": { "day": "FRIDAY", "time": "00:00" }, "close": null },
          { "open": { "day": "SATURDAY", "time": "10:00" }, "close": {"day": "SATURDAY", "time": "15:00" } }
        ],
        "weekdayText": [
          "Måndag: 08 – 19",
          "Tisdag: 08 – 19",
          "Onsdag: 08 – 19",
          "Torsdag: 08 – 19",
          "Fredag: Öppet dygnet runt",
          "Lördag: 10 – 15",
          "Söndag: Stängt"
        ]
      }
    }
  ]
}
JSON);
        $boxes = $client->boxes()->getAvailableLockers('SE', '11247');

        $lastRequest = $client->getLastRequest();

        self::assertCount(1, $boxes);
        self::assertNotNull($lastRequest);
        self::assertSame('https://api.budbee.com/boxes/postalcodes/validate/SE/11247', (string) $lastRequest->getUri());

        foreach ($boxes as $box) {
            self::assertInstanceOf(Box::class, $box);
        }
    }

    /**
     * @test
     */
    public function it_gets_box_by_identifier(): void
    {
        $client = $this->createClient(<<<JSON
{
    "id": "BOX0012",
    "address": {
        "street": "Ehlersvej 11",
        "postalCode": "2900",
        "city": "Hellerup",
        "country": "DK",
        "coordinate": {
            "latitude": 55.72806157181675,
            "longitude": 12.57328965570486
        }
    },
    "name": "Budbee CPH Office",
    "directions": "The Budbee Box is found inside the building behind the reception on the first floor.",
    "openingHours": {
        "periods": [
            {
                "open": {
                    "day": "MONDAY",
                    "time": "11:00"
                },
                "close": {
                    "day": "MONDAY",
                    "time": "21:00"
                }
            },
            {
                "open": {
                    "day": "TUESDAY",
                    "time": "11:00"
                },
                "close": {
                    "day": "TUESDAY",
                    "time": "21:00"
                }
            },
            {
                "open": {
                    "day": "WEDNESDAY",
                    "time": "11:00"
                },
                "close": {
                    "day": "WEDNESDAY",
                    "time": "21:00"
                }
            },
            {
                "open": {
                    "day": "THURSDAY",
                    "time": "11:00"
                },
                "close": {
                    "day": "THURSDAY",
                    "time": "21:00"
                }
            },
            {
                "open": {
                    "day": "FRIDAY",
                    "time": "11:00"
                },
                "close": {
                    "day": "FRIDAY",
                    "time": "21:00"
                }
            },
            {
                "open": {
                    "day": "SATURDAY",
                    "time": "11:00"
                },
                "close": {
                    "day": "SATURDAY",
                    "time": "21:00"
                }
            },
            {
                "open": {
                    "day": "SUNDAY",
                    "time": "00:00"
                },
                "close": {
                    "day": "SUNDAY",
                    "time": "00:00"
                }
            }
        ],
        "weekdayText": [
            "Mon: 11 – 21",
            "Tue: 11 – 21",
            "Wed: 11 – 21",
            "Thu: 11 – 21",
            "Fri: 11 – 21",
            "Sat: 11 – 21",
            "Sun: 00 – 00"
        ]
    }
}
JSON);
        $box = $client->boxes()->getLockerByIdentifier('BOX0012');

        $lastRequest = $client->getLastRequest();

        self::assertNotNull($box);
        self::assertNotNull($lastRequest);
        self::assertSame('https://api.budbee.com/boxes/BOX0012', (string) $lastRequest->getUri());
    }

    private function createClient(string $returnedJson): ClientInterface
    {
        $httpClient = $this->prophesize(HttpClientInterface::class);
        $httpClient->sendRequest(Argument::any())->willReturn(new Response(200, [], $returnedJson));
        $client = new Client('apiKey', 'apiSecret');
        $client->setHttpClient($httpClient->reveal());

        return $client;
    }
}
