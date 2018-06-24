<?php
/**
 * This file is part of the php-poc-geoapi console application.
 *
 * (c) Bruno Ricardo Siqueira <brunoric@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Tests\Entity;

use PHPUnit\Framework\TestCase;
use App\Entity\Customer;

class CustomerTest extends TestCase
{
    /**
     * @covers \App\Entity\Customer
     * @param string $customerJsonData
     */
    public function testBuildFromJson($customerJsonData = '{"latitude": "52.986375", "user_id": 12, "name": "Christina McArdle", "longitude": "-6.043701"}')
    {
        $customer = Customer::buildFromJson($customerJsonData);
        $this->assertInstanceOf('App\Entity\Customer', $customer);
    }

    /**
     * The first 4 values in this provider are just for boundaries check. The other results are a crosscheck with the
     * online calculator provided below as a link.
     *
     * @see http://www.meridianoutpost.com/resources/etools/calculators/calculator-latitude-longitude-distance.php
     *
     * @return array
     */
    public function coordinatesProvider()
    {
        return [
            [53.339428, -6.257664, 0],
            [10, 10, 0, 10, 10],
            [10, -10, 0, 10, -10],
            [-10, 10, 0, -10, 10],
            [51.92893, -10.27699, 313240],
            [51.8856167, -10.4240951, 324360],
            [52.986375, -6.043701, 41770],
            [53.2451022, -6.238335, 10570],
        ];
    }

    /**
     * @dataProvider coordinatesProvider
     * @param float $latitude
     * @param float $longitude
     * @param float $expectedDistance in meters
     * @param float $latitudeFrom
     * @param float $longitudeFrom
     * @param int $acceptedRoundingError in meters
     */
    public function testCalculateDistanceTo(
        float $latitude,
        float $longitude,
        float $expectedDistance,
        float $latitudeFrom = 53.339428,
        float $longitudeFrom = -6.257664,
        int $acceptedRoundingError = 20
    )
    {
        $result = Customer::calculateDistanceTo($latitude, $longitude, $latitudeFrom, $longitudeFrom);

        $this->assertGreaterThanOrEqual($expectedDistance - $acceptedRoundingError, $result);
        $this->assertLessThanOrEqual($expectedDistance + $acceptedRoundingError, $result);
    }
}
