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

namespace App\Tests\Repository;

use App\Entity\Customer;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Repository\CustomerS3Repository;
use Psr\Log\NullLogger;
use GuzzleHttp\Exception\ClientException;

class CustomerS3RepositoryTest extends WebTestCase
{
    protected $logger;

    public function setUp()
    {
        parent::setUp();

        $this->logger = new NullLogger();
    }

    public function validPayloadProvider()
    {
        $payload = <<<'PAYLOAD'
{"latitude": "52.986375", "user_id": 12, "name": "Christina McArdle", "longitude": "-6.043701"}
{"latitude": "51.92893", "user_id": 1, "name": "Alice Cahill", "longitude": "-10.27699"}
PAYLOAD;

        return [
            [
                $payload,
                [
                    new Customer(12, 'Christina McArdle', 52.986375,  -6.043701),
                    new Customer(1, 'Alice Cahill', 51.92893, -10.27699),
                ]
            ]
        ];
    }

    /**
     * @dataProvider validPayloadProvider
     * @covers \App\Repository\CustomerS3Repository::logClientException
     * @covers \App\Repository\CustomerS3Repository::logGenericException
     * @covers \App\Repository\CustomerS3Repository::parseResultsFromResponse
     * @param string $payload
     * @param array $expectedResults
     */
    public function testParseResultsFromResponse_valid_payload(string $payload, array $expectedResults)
    {
        $responseMock = $this
            ->getMockBuilder(\stdClass::class)
            ->setMethods(['getStatusCode', 'getBody'])
            ->getMock();
        $responseMock
            ->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(200);
        $responseMock
            ->expects($this->once())
            ->method('getBody')
            ->willReturn($payload);

        $customerRepositoryMock = $this
            ->getMockBuilder(CustomerS3Repository::class)
            ->setConstructorArgs([$this->logger])
            ->setMethods(['getResponse'])
            ->getMock();
        $customerRepositoryMock
            ->expects($this->once())
            ->method('getResponse')
            ->willReturn($responseMock);

        /** @var CustomerS3Repository $customerRepositoryMock */
        $results = $customerRepositoryMock->parseResultsFromResponse();

        $this->assertEquals($expectedResults, $results);
    }

    public function testParseResultsFromResponse_generic_exception()
    {
        $responseMock = $this
            ->getMockBuilder(\stdClass::class)
            ->setMethods(['getStatusCode', 'getBody'])
            ->getMock();
        $responseMock
            ->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(404);
        $responseMock
            ->expects($this->never())
            ->method('getBody');

        $customerRepositoryMock = $this
            ->getMockBuilder(CustomerS3Repository::class)
            ->setConstructorArgs([$this->logger])
            ->setMethods(['getResponse', 'logGenericException'])
            ->getMock();
        $customerRepositoryMock
            ->expects($this->once())
            ->method('getResponse')
            ->willReturn($responseMock);
        $customerRepositoryMock
            ->expects($this->once())
            ->method('logGenericException')
            ->willReturnSelf();

        /** @var CustomerS3Repository $customerRepositoryMock */
        $results = $customerRepositoryMock->parseResultsFromResponse();

        $this->assertEquals([], $results);
    }

    public function testParseResultsFromResponse_client_exception()
    {
        $customerRepositoryMock = $this
            ->getMockBuilder(CustomerS3Repository::class)
            ->setConstructorArgs([$this->logger])
            ->setMethods(['getResponse', 'logClientException'])
            ->getMock();
        $customerRepositoryMock
            ->expects($this->once())
            ->method('getResponse')
            ->willThrowException(
                new ClientException(
                    'dummyMessage',
                    new Request('GET', 'dummyRequest'),
                    new Response()
                )
            );
        $customerRepositoryMock
            ->expects($this->once())
            ->method('logClientException')
            ->willReturnSelf();

        /** @var CustomerS3Repository $customerRepositoryMock */
        $results = $customerRepositoryMock->parseResultsFromResponse();

        $this->assertEquals([], $results);
    }

    public function criteriaAndCustomersProvider()
    {
        return [
            [
                ['name', 'asc'],
                [
                    new Customer(12, 'Christina McArdle', 52.986375, -6.043701),
                    new Customer(1, 'Alice Cahill', 51.92893, -10.27699),
                ],
                [
                    new Customer(1, 'Alice Cahill', 51.92893, -10.27699),
                    new Customer(12, 'Christina McArdle', 52.986375, -6.043701),
                ],
            ],
            [
                ['name', 'desc'],
                [
                    new Customer(12, 'Christina McArdle', 52.986375, -6.043701),
                    new Customer(1, 'Alice Cahill', 51.92893, -10.27699),
                ],
                [
                    new Customer(12, 'Christina McArdle', 52.986375, -6.043701),
                    new Customer(1, 'Alice Cahill', 51.92893, -10.27699),
                ],
            ],
            [
                ['id', 'asc'],
                [
                    new Customer(12, 'Christina McArdle', 52.986375, -6.043701),
                    new Customer(1, 'Alice Cahill', 51.92893, -10.27699),
                ],
                [
                    new Customer(1, 'Alice Cahill', 51.92893, -10.27699),
                    new Customer(12, 'Christina McArdle', 52.986375, -6.043701),
                ],
            ],
            [
                ['id', 'desc'],
                [
                    new Customer(12, 'Christina McArdle', 52.986375, -6.043701),
                    new Customer(1, 'Alice Cahill', 51.92893, -10.27699),
                ],
                [
                    new Customer(12, 'Christina McArdle', 52.986375, -6.043701),
                    new Customer(1, 'Alice Cahill', 51.92893, -10.27699),
                ],
            ],
            [
                ['latitude', 'asc'],
                [
                    new Customer(12, 'Christina McArdle', 52.986375, -6.043701),
                    new Customer(1, 'Alice Cahill', 51.92893, -10.27699),
                ],
                [
                    new Customer(1, 'Alice Cahill', 51.92893, -10.27699),
                    new Customer(12, 'Christina McArdle', 52.986375, -6.043701),
                ],
            ],
            [
                ['latitude', 'desc'],
                [
                    new Customer(12, 'Christina McArdle', 52.986375, -6.043701),
                    new Customer(1, 'Alice Cahill', 51.92893, -10.27699),
                ],
                [
                    new Customer(12, 'Christina McArdle', 52.986375, -6.043701),
                    new Customer(1, 'Alice Cahill', 51.92893, -10.27699),
                ],
            ],
            [
                ['longitude', 'asc'],
                [
                    new Customer(12, 'Christina McArdle', 52.986375, -6.043701),
                    new Customer(1, 'Alice Cahill', 51.92893, -10.27699),
                ],
                [
                    new Customer(1, 'Alice Cahill', 51.92893, -10.27699),
                    new Customer(12, 'Christina McArdle', 52.986375, -6.043701),
                ],
            ],
            [
                ['longitude', 'desc'],
                [
                    new Customer(12, 'Christina McArdle', 52.986375, -6.043701),
                    new Customer(1, 'Alice Cahill', 51.92893, -10.27699),
                ],
                [
                    new Customer(12, 'Christina McArdle', 52.986375, -6.043701),
                    new Customer(1, 'Alice Cahill', 51.92893, -10.27699),
                ],
            ],
        ];
    }

    /**
     * @dataProvider criteriaAndCustomersProvider
     * @covers \App\Repository\CustomerS3Repository::numericComparison
     * @covers \App\Repository\CustomerS3Repository::stringComparison
     * @covers \App\Repository\CustomerS3Repository::orderByCriteria
     * @param array $criteria
     * @param array $customers
     * @param array $orderedCustomers
     */
    public function testOrderByCriteria(array $criteria, array $customers, array $orderedCustomers)
    {
        $customerRepositoryMock = $this
            ->getMockBuilder(CustomerS3Repository::class)
            ->setConstructorArgs([$this->logger])
            ->setMethods(['parseResultsFromResponse'])
            ->getMock();
        $customerRepositoryMock
            ->expects($this->exactly(intval(empty($order))))
            ->method('parseResultsFromResponse')
            ->willReturn($customers);

        /** @var CustomerS3Repository $customerRepositoryMock */
        $results = $customerRepositoryMock->orderByCriteria($criteria[0], $criteria[1]);

        $this->assertEquals($orderedCustomers, $results);
    }

    public function orderProvider()
    {
        return [
            [null],
            ['by_name_asc'],
            ['by_distance_asc'],
            ['dummy'],
        ];
    }

    /**
     * @dataProvider orderProvider
     * @covers \App\Repository\CustomerS3Repository::testFetchAll
     * @param string|null $order
     */
    public function testFetchAll($order = null)
    {
        $customerRepositoryMock = $this
            ->getMockBuilder(CustomerS3Repository::class)
            ->setConstructorArgs([$this->logger])
            ->setMethods(['orderByCriteria', 'parseResultsFromResponse'])
            ->getMock();

        $customerRepositoryMock
            ->expects($this->exactly(intval(!empty($order))))
            ->method('orderByCriteria')
            ->willReturn([]);

        $customerRepositoryMock
            ->expects($this->exactly(intval(empty($order))))
            ->method('parseResultsFromResponse')
            ->willReturn([]);

        /** @var CustomerS3Repository $customerRepositoryMock */
        $customerRepositoryMock->fetchAll($order);
    }
}
