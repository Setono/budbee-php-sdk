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
    public function it_finds_boxes_by_country_code_and_postal_code(): void
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

    private function createClient(string $returnedJson): ClientInterface
    {
        $httpClient = $this->prophesize(HttpClientInterface::class);
        $httpClient->sendRequest(Argument::any())->willReturn(new Response(200, [], $returnedJson));
        $client = new Client('apiKey', 'apiSecret');
        $client->setHttpClient($httpClient->reveal());

        return $client;
    }
}
