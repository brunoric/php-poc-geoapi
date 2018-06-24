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

namespace App\Entity;

class Customer
{
    /**
     * @var integer
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var float
     */
    public $latitude;

    /**
     * @var float
     */
    public $longitude;

    /**
     * @var float
     */
    public $distance;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return float
     */
    public function getLatitude(): float
    {
        return $this->latitude;
    }

    /**
     * @param float $latitude
     */
    public function setLatitude(float $latitude): void
    {
        $this->latitude = $latitude;
    }

    /**
     * @return float
     */
    public function getLongitude(): float
    {
        return $this->longitude;
    }

    /**
     * @param float $longitude
     */
    public function setLongitude(float $longitude): void
    {
        $this->longitude = $longitude;
    }

    /**
     * @return float
     */
    public function getDistance(): float
    {
        return $this->distance;
    }

    /**
     * @param float $distance
     */
    public function setDistance(float $distance): void
    {
        $this->distance = $distance;
    }

    /**
     * Customer constructor.
     * @param int $id
     * @param string $name
     * @param float $latitude
     * @param float $longitude
     * @param null|float $distance
     */
    public function __construct(int $id, string $name, float $latitude, float $longitude, ?float $distance = null)
    {
        $this->setId($id);
        $this->setName($name);
        $this->setLatitude($latitude);
        $this->setLongitude($longitude);
        $this->setDistance($distance?:$this->calculateDistanceTo($latitude, $longitude));
    }

    /**
     * @param string $customerJsonData
     * @return Customer
     */
    public static function buildFromJson(string $customerJsonData): self
    {
        $customer = json_decode($customerJsonData, true);
        return new self(
            $customer['user_id'],
            $customer['name'],
            floatval($customer['latitude']),
            floatval($customer['longitude'])
        );
    }

    /**
     * Calculates distance between two points on Earth using the Vincenty formula.
     *
     * @see https://en.wikipedia.org/wiki/Great-circle_distance
     *
     * @param float $latitude
     * @param float $longitude
     * @param float $latitudeFrom
     * @param float $longitudeFrom
     * @param float $radius of earth in meters
     * @return float Distance in meters
     */
    public static function calculateDistanceTo(
        float $latitude,
        float $longitude,
        float $latitudeFrom = 53.339428,
        float $longitudeFrom = -6.257664,
        float $radius = 6371000
    )
    {
        $latitudeA = deg2rad($latitudeFrom);
        $longitudeA = deg2rad($longitudeFrom);
        $latitudeB = deg2rad($latitude);
        $longitudeB = deg2rad($longitude);

        $longitudeDelta = $longitudeB - $longitudeA;
        $a = pow(cos($latitudeB) * sin($longitudeDelta), 2) +
            pow(cos($latitudeA) * sin($latitudeB) - sin($latitudeA) * cos($latitudeB) * cos($longitudeDelta), 2);
        $b = sin($latitudeA) * sin($latitudeB) + cos($latitudeA) * cos($latitudeB) * cos($longitudeDelta);

        return atan2(sqrt($a), $b) * $radius;
    }
}