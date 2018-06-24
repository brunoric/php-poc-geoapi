<?php
/**
 * This file is part of the php-poc-geoapi console application.
 *
 * (c) Bruno Ricardo Siqueira <brunoric@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Repository;

use App\Entity\Customer;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException as ClientException;
use Psr\Log\LoggerInterface;

class CustomerS3Repository implements RepositoryInterface
{
    const CUSTOMER_S3_LIST = 'https://s3.amazonaws.com/intercom-take-home-test/customers.txt';
    const MSG_ERROR_S3_FETCH = 'Error: Unable to fetch from S3';
    const MSG_ERROR_S3_FORMAT = 'Error: Data format invalid from S3';

    protected $httpClient;
    protected $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->httpClient = new Client();
        $this->logger = $logger;
    }

    /**
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws ClientException
     */
    public function getResponse()
    {
        return $this->httpClient->request('GET', self::CUSTOMER_S3_LIST);
    }

    /**
     * @return array
     */
    public function parseResultsFromResponse()
    {
        try
        {
            $response = $this->getResponse();

            if ($response->getStatusCode() != 200) {
                throw new \Exception(self::MSG_ERROR_S3_FETCH);
            }

            $payload = $response->getBody();

            $rows = explode(PHP_EOL, $payload);

            if (!is_array($rows)) {
                throw new \Exception(self::MSG_ERROR_S3_FORMAT);
            }

            $customers = [];
            foreach ($rows as $row) {
                $customers[] = Customer::buildFromJson($row);
            }

            return $customers;

        }
        catch (ClientException $exception)
        {
            $this->logClientException($exception);

            return [];
        }
        catch (\Exception $exception)
        {
            $this->logGenericException($exception);

            return [];
        }
    }

    /**
     * @param ClientException $exception
     */
    protected function logClientException(ClientException $exception)
    {
        $this->logger->error(
            $exception->getMessage(),
            [
                'exception_type' => ClientException::class,
                'exception_trace' => $exception->getTraceAsString()
            ]
        );
    }

    /**
     * @param \Exception $exception
     */
    protected function logGenericException(\Exception $exception)
    {
        $this->logger->error(
            $exception->getMessage(),
            [
                'exception_type' => \Exception::class,
                'exception_trace' => $exception->getTraceAsString()
            ]
        );
    }

    protected function numericComparison($a, $b, $direction)
    {
        if ($direction == 'asc') {
            return $a - $b;
        }

        return $b - $a;
    }

    protected function stringComparison($a, $b, $direction)
    {
        if ($direction == 'asc') {
            return strcmp($a, $b);
        }
        return strcmp($b, $a);
    }

    public function orderByCriteria($property = 'name', $direction = 'asc')
    {
        $results = $this->parseResultsFromResponse();
        usort($results, function($a, $b) use ($property, $direction) {

            $propertyA = $a->{$property};
            $propertyB = $b->{$property};

            if (is_numeric($propertyA) && is_numeric($propertyB)) {
                return $this->numericComparison($propertyA, $propertyB, $direction);
            }

            return $this->stringComparison($propertyA, $propertyB, $direction);
        });

        return $results;
    }

    /**
     * @param null|string $property
     * @param null|string $direction
     * @return array
     */
    public function fetchAll(?string $property = null, ?string $direction = null): array
    {
        if (!$property && !$direction) {
            return $this->parseResultsFromResponse();
        }

        return $this->orderByCriteria($property, $direction);
    }

    /**
     * @param int $id
     * @return Customer
     */
    public function fetch(int $id): Customer
    {
        // TODO: Implement fetch() method.
        return new Customer(1, 1, 1, 1);
    }
}