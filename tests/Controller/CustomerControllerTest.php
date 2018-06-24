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

namespace App\Tests\Controller;

use Psr\Log\NullLogger;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CustomerControllerTest extends WebTestCase
{
    protected $logger;

    public function setUp()
    {
        parent::setUp();

        $this->logger = new NullLogger();
    }

    /**
     * @return array
     */
    public function criteriaProvider()
    {
        return [
            ['name', 'asc'],
            ['name', 'desc'],
            ['id', 'asc'],
            ['id', 'desc'],
            ['latitude', 'asc'],
            ['latitude', 'desc'],
            ['longitude', 'asc'],
            ['longitude', 'desc'],
        ];
    }

    /**
     * E2E functional test. Not part of the default UT suite.
     *
     * @dataProvider criteriaProvider
     * @param string $property
     * @param string $order
     */
    public function testList($property, $order)
    {
        $client = static::createClient();
        $client->request('GET', "/customer/list/$property/$order");
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
