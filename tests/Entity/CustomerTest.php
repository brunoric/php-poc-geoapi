<?php

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
}
