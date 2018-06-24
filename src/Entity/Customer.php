<?php
/**
 * This file is part of the php-poc-geoapi console application.
 *
 * (c) Bruno Ricardo Siqueira <brunoric@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
     * Customer constructor.
     * @param int $id
     * @param string $name
     * @param float $latitude
     * @param float $longitude
     */
    public function __construct(int $id, string $name, float $latitude, float $longitude)
    {
        $this->setId($id);
        $this->setName($name);
        $this->setLatitude($latitude);
        $this->setLongitude($longitude);
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
            $customer['latitude'],
            $customer['longitude']
        );
    }
}